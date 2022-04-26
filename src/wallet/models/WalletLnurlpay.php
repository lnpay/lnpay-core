<?php

namespace lnpay\wallet\models;

use lnpay\behaviors\JsonDataBehavior;
use lnpay\behaviors\UserAccessKeyBehavior;
use lnpay\components\HelperComponent;
use lnpay\models\action\ActionName;
use lnpay\models\CustyDomain;
use lnpay\models\StatusType;
use lnpay\models\User;
use lnpay\models\UserAccessKey;
use lnpay\wallet\models\Wallet;
use Yii;

/**
 * This is the model class for table "wallet_lnurlpay".
 *
 * @property int $id
 * @property int $created_at
 * @property int $updated_at
 * @property int $user_id
 * @property int $wallet_id
 * @property int $status_type_id
 * @property string $external_hash
 * @property string $user_label
 * @property string|null $json_data
 * @property string|null $lnurl_encoded
 * @property string|null $lnurl_decoded
 * @property int|null $lnurlp_minSendable_msat
 * @property int|null $lnurlp_maxSendable_msat
 * @property string|null $lnurlp_short_desc
 * @property string|null $lnurlp_successAction
 * @property string|null $lnurlp_identifier
 * @property int|null $lnurlp_commentAllowed
 * @property string|null $lnurlp_success_message
 * @property string|null $lnurlp_success_url
 * @property string|null $lnurlp_image_base64
 * @property string|null $lnurlp_metadata
 *
 * @property User $user
 * @property Wallet $wallet
 * @property StatusType $statusType
 */
class WalletLnurlpay extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wallet_lnurlpay';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            \yii\behaviors\TimestampBehavior::class,
            JsonDataBehavior::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'wallet_id'], 'required'],
            [['status_type_id'],'default','value'=>StatusType::WALLET_LNURL_ACTIVE],
            [['lnurlp_short_desc'],'default','value'=>'LNURL PAY (via LNPay.co)'],
            ['lnurlp_minSendable_msat', 'compare', 'compareValue' => 1000, 'operator' => '>=', 'type' => 'number'],
            [['lnurlp_minSendable_msat'],'default','value'=>1000],
            [['lnurlp_maxSendable_msat'],'default','value'=>function ($model) {

                if (\LNPay::$app instanceof \yii\web\Application && !YII_ENV_TEST) {
                    if (\LNPay::$app->user->identity) { //if user is logged in
                        return \LNPay::$app->user->identity->getJsonData(User::DATA_MAX_DEPOSIT) * 1000;
                    } else { //if brand new user
                        return 100000000; //100,000 this is fee wallet / default wallets
                    }

                } else {
                    return 1000;
                }
            }
            ],
            [['external_hash'],'default','value'=>function(){ return 'lnurlp_'.HelperComponent::generateRandomString(18); }],
            [['id', 'user_id', 'wallet_id', 'status_type_id', 'lnurlp_minSendable_msat', 'lnurlp_maxSendable_msat', 'lnurlp_commentAllowed'], 'integer'],
            [['json_data', 'lnurlp_successAction', 'lnurlp_metadata'], 'safe'],
            [['custy_domain_id'],'domainOwner'],
            [['lnurl_encoded', 'lnurl_decoded', 'lnurlp_short_desc', 'lnurlp_success_message', 'lnurlp_success_url', 'lnurlp_image_base64'], 'string'],
            [['external_hash'], 'string', 'max' => 45],
            [['lnurlp_identifier','user_label'], 'string', 'max' => 255],
            [['lnurlp_identifier'],'match','pattern'=>'/^[a-zA-Z\s]+$/']
            ];
    }

    public function domainOwner($attribute,$params)
    {
        if (!$this->custy_domain_id)
            return;

        if ($d = CustyDomain::findOne($this->custy_domain_id)) {
            if ($d->user_id != \LNPay::$app->user->id) {
                $this->addError($attribute,'Domain does not belong to this user or org!');
            }
        } else {
            $this->addError($attribute,'Invalid domain ID specified');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'user_id' => 'User ID',
            'wallet_id' => 'Wallet ID',
            'user_label'=>'User Label',
            'status_type_id' => 'Status Type ID',
            'external_hash' => 'External Hash',
            'json_data' => 'Json Data',
            'lnurl_encoded' => 'Lnurl Encoded',
            'lnurl_decoded' => 'Lnurl Decoded',
            'lnurlp_minSendable_msat' => 'Lnurlp Min Sendable Msat',
            'lnurlp_maxSendable_msat' => 'Lnurlp Max Sendable Msat',
            'lnurlp_short_desc' => 'Lnurlp Short Desc',
            'lnurlp_successAction' => 'Lnurlp Success Action',
            'lnurlp_identifier' => 'Lnurlp Identifier',
            'lnurlp_commentAllowed' => 'Lnurlp Comment Allowed',
            'lnurlp_success_message' => 'Lnurlp Success Message',
            'lnurlp_success_url' => 'Lnurlp Success Url',
            'lnurlp_image_base64' => 'Lnurlp Image Base64',
            'lnurlp_metadata' => 'Lnurlp Metadata',
        ];
    }

    /**
     * @param $external_hash
     * @return WalletLnurlpay
     */
    public static function findByHash($external_hash)
    {
        return static::find()->where(['external_hash'=>$external_hash])->one();
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Gets query for [[Wallet]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWallet()
    {
        return $this->hasOne(Wallet::className(), ['id' => 'wallet_id']);
    }

    /**
     * Gets query for [[StatusType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatusType()
    {
        return $this->hasOne(StatusType::className(), ['id' => 'status_type_id']);
    }

    /**
     * Gets query for [[CustyDomain]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCustyDomain()
    {
        return $this->hasOne(CustyDomain::className(), ['id' => 'custy_domain_id']);
    }

    /**
     * Generate new LNURL PAY record
     *
     * @return WalletLnurlpay
     */
    public static function generateNewModel($lnurlp_data=[],$metadata=[])
    {
        $model = new WalletLnurlpay();
        $model->load($lnurlp_data,'');
        $model->json_data = $metadata;

        return $model;
    }

    /**
     * Get is this lnurl active
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->status_type_id == StatusType::WALLET_LNURL_ACTIVE;
    }

    /**
     * Formulate metadata right
     *
     * @return boolean
     */
    public function formulateMetadata()
    {
        $array = [];

        //short desc
        $short_desc = [
            'text/plain',
            $this->lnurlp_short_desc
        ];

        $array[] = $short_desc;

        return json_encode($array);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                //generate LNURLS

                $baseUrl = ["/v1/wallet/{$this->wallet->getFirstAccessKeyByRole(UserAccessKeyBehavior::ROLE_WALLET_LNURL_PAY)}/lnurlp/{$this->external_hash}"];
                $this->lnurl_decoded = \LNPay::$app->urlManager->createAbsoluteUrl($baseUrl);
                $this->lnurl_encoded = \tkijewski\lnurl\encodeUrl($this->lnurl_decoded);

                $this->lnurlp_metadata = $this->formulateMetadata();
            }

            return true;
        } else {
            return false;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {

        }
    }




    /**
     *
     *
     * API RESPONSE FIELDS
     *
     *
     */

    public function fields()
    {
        $fields = parent::fields();
        $fields['id'] = $fields['external_hash'];
        $fields['statusType'] = 'statusType';
        $fields['wallet_id'] = function ($model) {
            return $model->wallet->external_hash;
        };
        $fields['custyDomain'] = 'custyDomain';

        // remove fields that contain sensitive information
        unset($fields['json_data'],
            $fields['user_id'],
            $fields['status_type_id'],
            $fields['custy_domain_id'],
            $fields['external_hash'],
            $fields['lnurlp_successAction'],
            $fields['lnurlp_commentAllowed'],
            $fields['lnurlp_success_message'],
            $fields['lnurlp_success_url'],
            $fields['lnurlp_image_base64'],
            $fields['lnurlp_metadata'],

            $fields['user_id'],
        );

        return $fields;

    }
}
