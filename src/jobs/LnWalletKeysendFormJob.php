<?php
namespace lnpay\jobs;

use lnpay\behaviors\UserAccessKeyBehavior;
use lnpay\components\AnalyticsComponent;
use lnpay\wallet\models\LnWalletKeysendForm;
use lnpay\wallet\models\Wallet;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use Yii;

class LnWalletKeysendFormJob extends \yii\base\BaseObject implements \yii\queue\RetryableJobInterface
{
    public $access_key;
    public $wallet_id;
    public $bodyParams = [];

    public function execute($queue)
    {
        $wallet = Wallet::findOne($this->wallet_id);
        $model = new LnWalletKeysendForm();

        $model->load($this->bodyParams, '');
        $model->wallet_id = $wallet->publicId;

        $array = [];
        if ($passThru = @$this->bodyParams['passThru']) {
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

        if (isset($this->bodyParams['custom_records']) && is_array($this->bodyParams['custom_records'])) {
            foreach ($this->bodyParams['custom_records'] as $key => $potentialArray) {
                if (is_array($potentialArray)) {
                    $this->bodyParams['custom_records'][$key] = addcslashes(json_encode($potentialArray),'\\');
                }
            }
            $model->custom_records = $this->bodyParams['custom_records'];
        }

        $model->passThru = $array;

        $wtx = $model->processKeysend(['method'=>'api']);

        return $wtx->id;
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