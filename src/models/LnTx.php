<?php

namespace lnpay\models;

use lnpay\behaviors\JsonDataBehavior;
use lnpay\components\HelperComponent;
use lnpay\node\components\LndNodeConnector;
use lnpay\models\action\ActionName;
use lnpay\wallet\models\Wallet;
use lnpay\wallet\models\WalletLnurlpay;
use lnpay\wallet\models\WalletTransaction;
use lnpay\wallet\models\WalletTransactionType;
use lnpay\node\models\LnNode;
use Lnrpc\HopHint;
use Lnrpc\RouteHint;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\ServerErrorHttpException;

/**
 * This is the model class for table "ln_tx".
 *
 * @property int $id
 * @property int $created_at
 * @property int $updated_at
 * @property int $user_id
 * @property string $dest_pubkey
 * @property string $payment_request
 * @property string $hash
 * @property string $memo
 * @property int $num_satoshis
 * @property int $expiry
 * @property string $payment_hash
 * @property string $payment_preimage
 * @property int $settled
 * @property int $settled_at
 * @property string $json_data
 *
 * @property FaucetLnTx[] $faucetLnTxes
 * @property User $user
 */
class LnTx extends \yii\db\ActiveRecord
{
    const LNPAY_DEFAULT_KEYSEND_WALLET = 5;

    public $_passThru = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ln_tx';
    }

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
            [['num_satoshis'], 'required'],
            [['created_at', 'updated_at', 'user_id', 'num_satoshis', 'expiry', 'settled', 'settled_at','expires_at','fee_msat'], 'integer'],
            [['expiry'],'default','value'=>86400*5], //5 days
            ['fee_msat','default','value'=>0],
            [['payment_request'], 'string'],
            ['passThru',function ($attribute, $params) {
                if(!is_array($this->$attribute)){
                    $this->addError($attribute,'Pass thru must be a valid JSON');
                }
            }],
            [['is_keysend'],'boolean'],
            [['dest_pubkey', 'r_hash_decoded', 'payment_preimage'], 'string', 'max' => 255],
            [['external_hash'],'default','value'=>'lntx_'.HelperComponent::generateRandomString(24)],
            [['custom_records'],'safe']
        ];
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
            'dest_pubkey' => 'Dest Pubkey',
            'payment_request' => 'Payment Request',
            'hash' => 'Hash',
            'memo' => 'Memo',
            'num_satoshis' => 'Num Satoshis',
            'expiry' => 'Expiry',
            'expires_at'=>'Expires at',
            'payment_hash' => 'Payment Hash',
            'payment_preimage' => 'Payment Preimage',
            'settled' => 'Settled',
            'settled_at' => 'Settled At',
            'json_data' => 'Json Data',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFaucetLnTxes()
    {
        return $this->hasMany(FaucetLnTx::className(), ['ln_tx_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLnNode()
    {
        return $this->hasOne(LnNode::className(), ['id' => 'ln_node_id']);
    }



    public function setPassThru($data)
    {
        if (!is_array($data))
            $this->_passThru = [];
        else
            $this->_passThru = $data;
    }

    public function getPassThru()
    {
        return $this->_passThru;
    }

    public function generateInvoice($checkLimits=false)
    {
        $this->user_id = ($this->user_id?:\LNPay::$app->user->id);
        $user = User::findOne($this->user_id);

        if (\LNPay::$app->user->isGuest || !\LNPay::$app->user->identity->getJsonData(User::DATA_IS_PAID_TIER))
            $this->memo = $this->memo. ' (via LNPAY.co)';

        $invoiceOptions = [
            'memo'=>$this->memo,
            'value'=>$this->num_satoshis,
            'expiry'=> (int) $this->expiry,
            'description_hash'=>hex2bin($this->description_hash)
        ];

        $hintNodeId = $user->getJsonData('hint_node_id');
        $hintChanId = $user->getJsonData('hint_chan_id');
        if ($hintNodeId && $hintChanId) {
            $hintOptions = [];
            $hopHint = new HopHint();
            $hopHint->setNodeId($hintNodeId);
            $hopHint->setChanId($hintChanId);

            $routeHint = new RouteHint();
            $routeHint->setHopHints([$hopHint]);
            $hintOptions = ['route_hints'=>[$routeHint]];

            $invoiceOptions = ArrayHelper::merge($invoiceOptions,$hintOptions);
        }


        if ($this->ln_node_id) {
            $node = LnNode::findOne($this->ln_node_id);
        } else {
            $node = $user->getLnNodeQuery()->one();
        }

        if ($checkLimits) {
            if ($max = $user->getJsonData(User::DATA_MAX_DEPOSIT)) {
                if ($this->num_satoshis > $max) {
                    throw new \yii\web\BadRequestHttpException('Receiving is limited to: '.$max.' satoshi per invoice');
                }
            } else {
                if ($this->num_satoshis > User::USER_GLOBAL_MAX_LIMIT_SATS) {
                    throw new \yii\web\BadRequestHttpException('Receiving is limited to: '.User::USER_GLOBAL_MAX_LIMIT_SATS.' satoshi per invoice');
                }
            }
        }

        $request = (object) $node->tryCreateInvoice($invoiceOptions);

        $this->ln_node_id = $node->id;
        $this->payment_request = $request->payment_request;
        $this->r_hash_decoded = bin2hex(base64_decode($request->r_hash));
        $this->dest_pubkey = $node->default_pubkey;
        $this->expires_at = time() + $this->expiry;

        if (!$this->save())
            throw new ServerErrorHttpException(HelperComponent::getFirstErrorFromFailedValidation($this));
        return $this;
    }

    public function getQrImage()
    {
        return 'https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl=' . $this->payment_request;
    }

    public function getIsInbound()
    {
        if ($this->lnNode) {
            if ($this->lnNode->default_pubkey==$this->dest_pubkey)
                return true;
            else
                return false;
        } else {
            return false;
        }

    }

    public function getActionAttributeData()
    {
        $attrs = ['num_satoshis','dest_pubkey','memo','settled_at'];
        $array = [];

        foreach ($attrs as $a) {
            $array['lntx_'.$a] = $this->{$a};
        }

        return $array;
    }

    /**
     * Settles invoice for first time
     * @param $invoice
     * @return LnTx|bool
     * @throws ServerErrorHttpException
     */
    public static function processInvoiceAction($invoice)
    {
        $lnTx = static::find()->where(['payment_request'=>$invoice['paymentRequest']])->one();

        if ($lnTx) {
            if ($lnTx->settled == 0) { //try and settle
                if (@$invoice['settled']) {
                    $lnTx->settled = 1;
                    $lnTx->num_satoshis = $invoice['amtPaidSat'];
                    $lnTx->payment_preimage = bin2hex(base64_decode($invoice['rPreimage']));
                    $lnTx->passThru = ArrayHelper::merge($lnTx->passThru,['htlc'=>@$invoice['htlcs'][0]]);
                    $lnTx->settled_at = time();
                    if ($lnTx->save()) {
                        //we are good
                    } else {
                        throw new ServerErrorHttpException(HelperComponent::getFirstErrorFromFailedValidation($lnTx));
                    }
                }
            }
            return $lnTx;
        }

        return FALSE;
    }

    /**
     * Settles KEYSEND INVOICE for first time
     * @param $invoice
     * @return LnTx|bool
     * @throws ServerErrorHttpException
     */
    public static function processSpontaneousInvoiceAction($invoice,$nodeObject)
    {
        $custom_records = [];
        if (@$invoice['amtPaidSat'] == 0) {
            return false;
        }

        if (@$invoice['isAmp']) {
            $preimage = bin2hex(@$invoice['htlcs'][0]['amp']['preimage']);
            $hash = bin2hex(@$invoice['htlcs'][0]['amp']['hash']);
        } else if (@$invoice['isKeysend']) {
            $preimage = bin2hex(base64_decode($invoice['rPreimage']));
            $hash = bin2hex($invoice['rHash']);
        }

        foreach ($invoice['htlcs'] as $htlc) {
            if ($htlc['customRecords']) {
                foreach ($htlc['customRecords'] as $key => $value) {
                    if ($key == LndNodeConnector::KEYSEND_TLV_KEY) {
                        $value = bin2hex($value);
                    } else {
                        $value = base64_decode($value);
                    }
                    $custom_records[$key]=$value;
                }
            }
        }



        $lnTx = new LnTx();
        $lnTx->user_id = NULL;
        $w = null;
        $tlv_key = null;
        $tlv_value = null;

        //check for wallet IDs
        foreach (LndNodeConnector::getThirdPartyTlvWalletIdKeys() as $key) {
            if (isset($custom_records[$key])) {
                $w = Wallet::findById($custom_records[$key]);
                $tlv_key = $key;
                $tlv_value = $custom_records[$key];
                break;
            }
        }

        //check for data keys
        foreach (LndNodeConnector::getThirdPartyTlvDataKeys() as $key) {
            if (isset($custom_records[$key])) {
                switch ($key) {
                    case LndNodeConnector::KEYSEND_PODCAST_KEY_DATA:
                    case LndNodeConnector::KEYSEND_SPADS_KEY:
                        $custom_records[$key] = @json_decode($custom_records[$key],TRUE);
                        break;
                }
            }
        }


        if (!$w) {
            $w = $nodeObject->keysendWallet;
        }
        $lnTx->user_id = $w->user_id;
        $lnTx->ln_node_id = $w->ln_node_id;
        $lnTx->num_satoshis = $invoice['amtPaidSat'];
        $lnTx->memo = 'keysend/amp: custom record ['.$tlv_key.'] ['.$tlv_value.']';
        $lnTx->dest_pubkey = $w->lnNode->default_pubkey;
        $lnTx->payment_request = 'keysend/amp';
        $lnTx->description_hash = @$invoice['descriptionHash'];
        $lnTx->r_hash_decoded = $hash;
        $lnTx->expiry = null;
        $lnTx->settled = (int)$invoice['settled'];
        $lnTx->settled_at = $invoice['settleDate'];
        $lnTx->expires_at = NULL;
        $lnTx->is_keysend = 1;
        $lnTx->is_amp = @$invoice['isAmp'];
        $lnTx->payment_addr = @$invoice['paymentAddr'];
        $lnTx->custom_records = new \yii\db\JsonExpression($custom_records);
        $lnTx->appendJsonData(['wallet_id'=>$w->external_hash]);
        if (@$invoice['settled']) {
            $lnTx->settled = 1;
            $lnTx->num_satoshis = $invoice['amtPaidSat'];
            $lnTx->payment_preimage = $preimage;
            $lnTx->settled_at = time();
            if ($lnTx->save()) {
                //If this is tied to a wallet
                    $wtx = new WalletTransaction();
                    $wtx->user_id = $lnTx->user_id;
                    $wtx->wallet_id = $w->id;
                    $wtx->num_satoshis = (int) $lnTx->num_satoshis;
                    $wtx->ln_tx_id = $lnTx->id;
                    $wtx->user_label = $lnTx->memo;
                    $wtx->passThru = ArrayHelper::merge($lnTx->passThru,['custom_records'=>$custom_records]);

                    if ($wtx->save()) {
                        //Hooray

                    } else {
                        throw new \Exception(HelperComponent::getFirstErrorFromFailedValidation($wtx));
                    }

                return $lnTx;
            } else {
                throw new ServerErrorHttpException(HelperComponent::getFirstErrorFromFailedValidation($lnTx));
            }
        }

        return FALSE;
    }



    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->appendJsonData($this->passThru);
            }

            return true;
        } else {
            return false;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (!$insert) { //When receiving funds this usually gets called, because inserts that aren't settled are waiting to be settled

            if ((@$changedAttributes['settled']===0) && ($this->settled === 1)) {

                //If this is tied to a wallet
                if ($this->getJsonData('wallet_id')) {
                    $wtx = new WalletTransaction();
                    $wtx->user_id = $this->user_id;
                    $wtx->wallet_id = Wallet::findByHash($this->getJsonData('wallet_id'))->id;
                    $wtx->num_satoshis = (int) $this->num_satoshis;
                    $wtx->ln_tx_id = $this->id;
                    $wtx->user_label = $this->memo;
                    $wtx->passThru = $this->passThru;

                    if ($lnurlp = $this->getJsonData('wallet_lnurlpay_id')) {
                        $lnurlpModel = WalletLnurlpay::findByHash($lnurlp);
                        $wtx->wallet_lnurlpay_id = $lnurlpModel->id;
                        $wtx->wtx_type_id = WalletTransactionType::LN_LNURL_PAY_INBOUND;
                    }


                    if ($wtx->save()) {
                        //yay
                    } else {
                        echo HelperComponent::getFirstErrorFromFailedValidation($wtx);
                        throw new \Exception(HelperComponent::getFirstErrorFromFailedValidation($wtx));
                    }
                }
            }
        }
    }















    /**
     *
     *
     * API STUFF
     */

    public function fields()
    {
        $fields = parent::fields();
        $fields['id'] = $fields['external_hash'];
        $fields['passThru'] = $fields['json_data'];

        // remove fields that contain sensitive information
        unset($fields['user_id'],$fields['node_id'],$fields['json_data'],$fields['external_hash'],$fields['updated_at']);

        return $fields;
    }
}
