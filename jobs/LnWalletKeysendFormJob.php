<?php
namespace app\jobs;

use app\behaviors\UserAccessKeyBehavior;
use app\components\AnalyticsComponent;
use app\models\wallet\LnWalletKeysendForm;
use app\models\wallet\Wallet;
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

        $model->passThru = $array;

        $wtx = $model->processKeysend(['method'=>'api']);

        return $wtx->id;
    }

    public function getTtr()
    {
        return 8;
    }

    public function canRetry($attempt, $error)
    {
        return ($attempt < 50) && ($error instanceof \app\exceptions\WalletBusyException);
    }
}