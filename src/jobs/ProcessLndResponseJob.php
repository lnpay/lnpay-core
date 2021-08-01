<?php
namespace lnpay\jobs;

use lnpay\node\models\LnNode;
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

        $url = getenv('LN_NODE_INGESTION_ENDPOINT').'/webhook-receiver/ln-node-ingestion';
        $response = $client->request('POST', $url, [
            'body' => json_encode([
                'responseObject'=>$this->responseObject,
                'nodeObject' => $this->nodeObject,
                'actionObject' => $this->actionObject
        ])]);

    }
}