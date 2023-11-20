<?php
namespace lnpay\jobs;

use lnpay\behaviors\UserAccessKeyBehavior;
use lnpay\components\AnalyticsComponent;
use lnpay\components\HelperComponent;
use lnpay\wallet\exceptions\UnableToPayLnurlpayException;
use lnpay\wallet\models\LnWalletLnurlpayPayForm;
use lnpay\wallet\models\LnWalletWithdrawForm;
use lnpay\wallet\models\Wallet;
use lnpay\wallet\models\WalletTransactionType;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use Yii;

class LnWalletLnurlPayFormJob extends \yii\base\BaseObject implements \yii\queue\RetryableJobInterface
{
    public $access_key;
    public $wallet_id;
    public $bodyParams = [];

    public function execute($queue)
    {
        $wallet = Wallet::findOne($this->wallet_id);
        $form = new LnWalletLnurlpayPayForm();
        $form->load($this->bodyParams,'');
        $form->probe_json = LnWalletLnurlpayPayForm::probe($form->lnurlpay_encoded??$form->ln_address);


        $array = [];
        $bp = $this->bodyParams;
        if ($passThru = @$bp['passThru']) {
            if (is_array($passThru)) {
                $array = $passThru;
            } else {
                try {
                    $array = Json::decode($passThru);
                } catch (\Throwable $t) {
                    throw new BadRequestHttpException('passThru data must be valid json');
                }
            }
        }
        if ($form->ln_address) {
            $array['target_ln_address'] = $form->ln_address;
        }
        if ($form->lnurlpay_encoded) {
            $array['target_lnurlp_encoded'] = $form->lnurlpay_encoded;
        }
        $form->passThru = $array;

        if ($form->validate()) {
            $invoice = $form->requestRemoteInvoice();

            $model = new LnWalletWithdrawForm();
            $model->payment_request = $invoice;
            $model->wallet_id = $wallet->id;
            $model->passThru = $form->passThru;
            $model->target_msat = $form->amt_msat;
            $model->wtx_type_id = WalletTransactionType::LN_LNURL_PAY_OUTBOUND;

            $wtx = $model->processWithdrawal(['method'=>'lnurlpay','lnurlp_comment'=>$form->comment]);
            return $wtx->id;

        } else {
            throw new UnableToPayLnurlpayException(HelperComponent::getFirstErrorFromFailedValidation($form));
        }
    }

    public function getTtr()
    {
        return 20;
    }

    public function canRetry($attempt, $error)
    {
        if (($attempt < 50) && ($error instanceof \lnpay\exceptions\WalletBusyException)) {
            return true;
        } else {
            return false;
        }
    }
}