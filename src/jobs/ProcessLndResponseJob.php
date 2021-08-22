<?php
namespace lnpay\jobs;

use lnpay\models\LnTx;
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
        $postBody = [
            'responseObject'=>$this->responseObject,
            'nodeObject' => $this->nodeObject,
            'actionObject' => $this->actionObject
        ];
        /*
        $response = $client->request('POST', $url, [
            'body' => json_encode($postBody)]);*/

        try {
            $this->processLndRpcEvent($postBody);

            //Last we register the action that LND has processed
            $nodeObject = LnNode::findOne($this->nodeObject['id']);
            $nodeObject->user->registerAction($this->actionObject['id'],$this->responseObject);

            //send to mongo
            if (getenv('MONGO_DB')) {
                $collection = Yii::$app->mongodb->getCollection($this->nodeObject['id'].'_'.$this->actionObject['id']);
                $collection->insert($this->responseObject);
            }

        } catch (\Throwable $t) {
            \LNPay::error($t->getMessage(),__METHOD__);
        }


    }

    public function processLndRpcEvent($postBody)
    {
        switch ($postBody['actionObject']['name']) {
            case 'Invoice':
                $invoice = $postBody['responseObject'];

                //Check for keysend payment
                try {
                    if (@$invoice['isKeysend']) {
                        $lnTx = LnTx::processKeysendInvoiceAction($invoice);
                        if ($lnTx)
                            return $lnTx->toArray();
                        else {
                            //keysend most likely sent from this node
                            return false;
                        }
                    }
                } catch (\Throwable $t) {
                    \LNPay::error($t->getMessage(),__METHOD__);
                }

                //check for normal invoice payment
                try {
                    $lnTx = LnTx::processInvoiceAction($invoice);
                } catch (\Throwable $t) {
                    \LNPay::error($t->getMessage(),__METHOD__);
                }

                if ($lnTx instanceof LnTx) {
                    return $lnTx->toArray();
                }

                break;
        }
    }
}