<?php
namespace app\jobs;

use app\modules\node\models\LnNode;
use Yii;

class ProcessLndResponseJob extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public $responseObject;
    public $nodeObject;
    public $actionObject;

    public function execute($queue)
    {
        $client = new \GuzzleHttp\Client([
            'http_errors'=>false,
            'headers' => ['Content-Type'=>'application/json']
        ]);

        $url = YII_ENV_PROD ? 'https://lnpay.co/webhook-receiver/lnd-catcher' : 'http://192.168.69.11/webhook-receiver/lnd-catcher';
        $response = $client->request('POST', $url, [
            'body' => json_encode([
                'responseObject'=>$this->responseObject,
                'nodeObject' => $this->nodeObject,
                'actionObject' => $this->actionObject
        ])]);

    }
}