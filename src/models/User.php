<?php
namespace lnpay\models;

use lnpay\behaviors\UserAccessKeyBehavior;
use lnpay\components\ActionComponent;
use lnpay\components\HelperComponent;
use lnpay\models\action\ActionName;
use lnpay\behaviors\JsonDataBehavior;
use lnpay\org\models\Org;
use lnpay\org\models\OrgUserType;
use lnpay\wallet\models\WalletTransaction;
use lnpay\wallet\models\WalletTransactionType;
use lnpay\wallet\models\WalletType;
use lnpay\node\models\LnNode;
use lnpay\wallet\models\Wallet;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\web\ServerErrorHttpException;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface,\vxm\mfa\IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;
    const STATUS_API_USER_LNTXBOT = 15;
    const STATUS_API_ADMIN = 100;

    const EMAIL_ACTIVATED = 2;

    const DATA_INVOICE_PAID_WH_URL = 'invoice_paid_webhook_url';
    const DATA_LNURL_OTT = 'lnurl_ott';
    const DATA_CUSTOM_LAYOUT = 'custom_layout';

    const DATA_IS_PAID_TIER = 'is_paid_tier';
    const DATA_INBOUND_SERVICE_FEE_RATE = 'inbound_service_fee_rate'; // .01 = 1%
    const DATA_OUTBOUND_SERVICE_FEE_RATE = 'outbound_service_fee_rate'; // .01 = 1%
    const DATA_FEE_TARGET_WALLET = 'fee_target_wallet';
    const DATA_FEE_TARGET_WALLET_EXTERNAL = 'external';
    const DATA_FEE_TARGET_WALLET_CONTAINED = 'contained';
    const DATA_MAX_DEPOSIT = 'max_receive_sats';
    const DATA_MAX_WITHDRAWAL = 'max_send_sats';
    const DATA_MAX_NETWORK_FEE_PERCENT = 'max_network_fee_percent';
    const DATA_IGNORE_WALLET_TRANSFER_MUTEX = 'ignore_wallet_transfer_mutex';

    //updates balance with a mysql transaction, instead of wallet_transaction table scan sum
    const DATA_STREAMING_QUERY_TRANSACTION_BALANCE_UPDATE = 'streaming_query_transaction_balance_update';

    const USER_GLOBAL_MAX_LIMIT_SATS = 10000; //current limit

    const CURRENT_API_VERSION = '2020-02-19';

    const DEFAULT_NODE_USER_ID = 6;

    private $_sessionApiKey;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            JsonDataBehavior::class,
            [
                'class'=>UserAccessKeyBehavior::class,
                'default_roles'=>[UserAccessKeyBehavior::ROLE_PUBLIC_API_KEY,UserAccessKeyBehavior::ROLE_SECRET_API_KEY]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['balance','api_parent_id','default_wallet_id','fee_wallet_id'],'integer'],
            ['external_hash', 'default', 'value' => 'usr_'.HelperComponent::generateRandomString(14)],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_API_ADMIN, self::STATUS_API_USER_LNTXBOT]],
            [['username','email','password','api_version'],'safe']
        ];
    }

    /**
     * get MFA KEY
     * @return mixed
     */
    public function getMfaSecretKey()
    {
        return $this->mfa_secret_key;
    }

    /**
     * set MFA KEY
     * @return mixed
     */
    public function createMfaSecretKey()
    {
        $this->mfa_secret_key = random_bytes(64);
        $this->save();
    }

    /**
     * Can we contact user? email for now, can be other stuff later
     * @return mixed
     */
    public function getEmailVerified()
    {
        return $this->email_verify;
    }

    public function getIsActivated()
    {
        return 1;
        //return $this->email_verify==self::EMAIL_ACTIVATED;
    }

    /**
     * @return mixed
     */
    public function confirmVerification()
    {
        $this->email_verify = 1;
        if ($this->save()) {

        } else {
            throw new ServerErrorHttpException('Unable to confirm user!');
        }
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::find()->where(['id' => $id])->andWhere(['>=','status',self::STATUS_ACTIVE])->one();
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $user = static::findOne(@UserAccessKey::find()->where(['access_key'=>$token])->one()->user_id);
        if ($user) {
            $user->sessionApiKey = $token;
            return $user;
        } else {
            return null;
        }
        //return static::findOne(['auth_key' => $token]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::find()->where(['username' => $username])->andWhere(['>=','status',self::STATUS_ACTIVE])->one();
    }

    /**
     * Finds user by email
     *
     * @param string $username
     * @return static|null
     */
    public static function findByEmail($username)
    {
        return static::find()->where(['email' => $username])->andWhere(['>=','status',self::STATUS_ACTIVE])->one();
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            //'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = 86400;
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * This is the actual (deprecated) auth_key field
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @return string apiKey used in current session (if API session)
     */
    public function getSessionApiKey()
    {
        return $this->_sessionApiKey;
    }

    /**
     * @param $apiKey
     */
    public function setSessionApiKey($apiKey)
    {
        $this->_sessionApiKey = $apiKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return \LNPay::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = \LNPay::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = \LNPay::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = \LNPay::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function getRateLimit($request, $action)
    {
        return [1000, 86400]; // $rateLimit requests per second
    }

    public function loadAllowance($request, $action)
    {
        return [$this->allowance, $this->allowance_updated_at];
    }

    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
        $this->allowance = $allowance;
        $this->allowance_updated_at = $timestamp;
        $this->save();
    }







    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLndInvoices()
    {
        return $this->hasMany(LndInvoice::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWallets()
    {
        return $this->hasMany(Wallet::className(), ['user_id' => 'id']);
    }

    /**
     * @return array|ActiveRecord|null
     */
    public function getDefaultWallet()
    {
        return Wallet::find()->where(['user_id'=>$this->id])->orderBy('id DESC')->one();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLinkLayouts()
    {
        return $this->hasMany(Layout::class, ['user_id' => 'id']);
    }

    public function getAvailableLayouts()
    {
        return Layout::find()->where(['user_id'=>$this->id])->orWhere(['id'=>Layout::defaultLayouts()])->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustyDomains()
    {
        return $this->hasMany(CustyDomain::class, ['user_id' => 'id']);
    }

    public function getAvailableDomains()
    {
        $defaultDomains = CustyDomain::defaultDomains();
        unset($defaultDomains[0]); //remove lnpay.co/t
        return CustyDomain::find()->where(['user_id'=>$this->id,'status_type_id'=>StatusType::CUSTYDOMAIN_ACTIVE])->orWhere(['id'=>$defaultDomains])->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLnNodeQuery()
    {
        $firstQuery = LnNode::find()->where(['user_id'=>$this->id])->orWhere(['org_id'=>$this->org_id,'is_custodian'=>1]);
        return $firstQuery;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLnNode()
    {
        return $this->hasOne(LnNode::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFeeWallet()
    {
        return $this->hasOne(Wallet::className(), ['id' => 'fee_wallet_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLnNodes()
    {
        return $this->hasMany(LnNode::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrg()
    {
        return $this->hasOne(Org::className(), ['id' => 'org_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrgUserType()
    {
        return $this->hasOne(OrgUserType::className(), ['id' => 'org_user_type_id']);
    }



    /**
     * @param int $sats
     * @return bool
     */
    public function getEligibleToWithdraw($sats=0)
    {
        \LNPay::debug("User: {$this->id} Available to withdraw:{$this->satsAvailableForWithdrawal} Amount withdrawing: $sats if 0, checking if balance > 0",__METHOD__);
        if ($this->satsAvailableForWithdrawal <= 0)
            return false;
        else if ($this->satsAvailableForWithdrawal < $sats)
            return false;
        else
            return true;
    }

    /**
     * Returns new balance
     * @param int $sats
     * @return int|mixed
     * @throws \yii\web\ServerErrorHttpException
     */
    public function addToBalance($sats=0) {
        \LNPay::info("Adding balance: $sats to user:".$this->id,__METHOD__);
        $this->balance += $sats;
        if ($this->save()) {
            return $this->balance;
        } else {
            throw new \yii\web\ServerErrorHttpException('Error updating balance');
        }
    }

    /**
     * @return int|mixed
     */
    public function getSatsAvailableForWithdrawal()
    {
        $sum = $this->balance;
        return $sum;
    }

    /**
     * @return int|mixed
     */
    public function getSatsEarned()
    {
        $paidInvoices = $this->getLndInvoices()->where(['settled'=>1])->all();
        $sum=0;
        foreach ($paidInvoices as $pI) {
            $sum += $pI->value;
        }
        return $sum;
    }

    public function registerAction($actionNameId,$data=[])
    {
        return ActionComponent::registerAction($actionNameId,$data,$this);
    }


    public function setTimeZone($tz)
    {
        $this->tz = $tz;
        \LNPay::$app->session->set('tz',$tz);
        return $this->save();
    }



    public function createDefaultSettings(): void
    {
        $this->json_data = [
            $this::DATA_IS_PAID_TIER => 0,
            $this::DATA_INBOUND_SERVICE_FEE_RATE   =>  0,
            $this::DATA_OUTBOUND_SERVICE_FEE_RATE  =>  0,
            $this::DATA_FEE_TARGET_WALLET  => $this::DATA_FEE_TARGET_WALLET_CONTAINED,
            $this::DATA_MAX_DEPOSIT => $this::USER_GLOBAL_MAX_LIMIT_SATS,
            $this::DATA_MAX_WITHDRAWAL => $this::USER_GLOBAL_MAX_LIMIT_SATS,
            $this::DATA_MAX_NETWORK_FEE_PERCENT => 5
        ];
        if (!$this->save()) {
            throw new \Exception('Error creating default settings:'.HelperComponent::getFirstErrorFromFailedValidation($this));
        }
    }

    public function createDefaultWallets(): void
    {
        $wallet = new Wallet();
        $wallet->user_label = 'Billing Wallet';
        $wallet->user_id = $this->id;
        $wallet->wallet_type_id = WalletType::FEE_WALLET;
        if (!$wallet->save()) {
            throw new \Exception('Error creating default wallets:'.HelperComponent::getFirstErrorFromFailedValidation($wallet));
        }
        $this->fee_wallet_id = $wallet->id;

        $this->save();
    }


    public function getServiceFeeRate($wtx_type): float
    {
        switch ($wtx_type) {
            case WalletTransactionType::LN_WITHDRAWAL:
                $fee = $this->getJsonData(self::DATA_OUTBOUND_SERVICE_FEE_RATE);
                break;
            case WalletTransactionType::LN_DEPOSIT:
                $fee = $this->getJsonData(self::DATA_INBOUND_SERVICE_FEE_RATE);
                break;
            case WalletTransactionType::LN_LOOP_OUT:
                $fee = $this->getJsonData(self::DATA_OUTBOUND_SERVICE_FEE_RATE);
                break;
        }

        if ($fee) {
            return (float) $fee;
        } else {
            return 0;
        }
    }

    public function getFeeTargetWallet()
    {
        if ($target = $this->getJsonData(self::DATA_FEE_TARGET_WALLET)) {
            return $target;
        } else {
            return self::DATA_FEE_TARGET_WALLET_CONTAINED;
        }
    }

    /**
     * Return total usage count of certain objects:
     * - Wallets
     * - LN Receive volume
     * - LN Send Volume
     * - Transfer Volume
     * - Webhooks
     */
    public function getWalletAPIUsageTotals()
    {

    }

    /**
     * Return usage by period of certain objects:
     * - LN Receive Volume
     * - LN Send Volume
     * @throws \yii\db\Exception
     */
    public function getWalletAPIUsageByPeriod($periodStart,$periodEnd=null)
    {
        if (!$periodEnd)
            $periodEnd = time();

        $a = [];
        $a['ln_inbound_volume'] = 0;
        $a['ln_outbound_volume'] = 0;

        $a['ln_inbound_volume'] = ((new \yii\db\Query())
            ->select('SUM(ABS(num_satoshis))')
            ->from('wallet_transaction')
            ->where(['>','created_at',$periodStart])
            ->andWhere(['<','created_at',$periodEnd])
            ->andWhere(['user_id'=>\LNPay::$app->user->id])
            ->andWhere(['wtx_type_id'=>[WalletTransactionType::LN_DEPOSIT,WalletTransactionType::LN_LNURL_PAY_INBOUND]])
            ->scalar()) ?? 0;

        $a['ln_outbound_volume'] = ((new \yii\db\Query())
                ->select('SUM(ABS(num_satoshis))')
                ->from('wallet_transaction')
                ->where(['>','created_at',$periodStart])
                ->andWhere(['<','created_at',$periodEnd])
                ->andWhere(['user_id'=>\LNPay::$app->user->id])
                ->andWhere(['wtx_type_id'=>[WalletTransactionType::LN_WITHDRAWAL,WalletTransactionType::LN_LNURL_PAY_OUTBOUND]])
                ->scalar()) ?? 0;

        return $a;
    }


    /**
     *
     * Email attributes to be sent for logic based email sending
     * For example, URLs to dashboard, name, etc.
     *
     * @return array
     *
     */
    public function getEmailAttributes()
    {
        $vars = [
        ];
        return $vars;
    }

    public function getApiVersion()
    {
        if (!$this->api_version) {
            $this->api_version = self::CURRENT_API_VERSION;
            $this->save();
        }

        if (\LNPay::$app instanceof \yii\web\Application) {
            return \LNPay::$app->request->getHeaders()->get('LNPay-Version') ?? $this->api_version;
        } else
            return $this->api_version;

    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $this->registerAction(ActionName::USER_CREATED,['email'=>$this->email]);

        } else {

        }
        parent::afterSave($insert, $changedAttributes);
    }










    /**
     *
     *
     *
     *
     * API FIELDS
     */

    public function fields()
    {
        $fields = parent::fields();
        $fields['id'] = $fields['external_hash'];

        // remove fields that contain sensitive information
        unset($fields['external_hash'],$fields['balance'], $fields['username'], $fields['mfa_secret_key'], $fields['auth_key'], $fields['password_hash'], $fields['password_reset_token'],$fields['updated_at'], $fields['api_parent_id'], $fields['json_data']);


        return $fields;
    }

}
