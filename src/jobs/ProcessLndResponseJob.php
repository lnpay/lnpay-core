<?php
namespace lnpay\jobs;

use lnpay\models\LnTx;
use lnpay\node\models\LnNode;
use Yii;
use yii\helpers\VarDumper;

class ProcessLndResponseJob extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public $responseArray;
    public $nodeArray;
    public $actionArray;

    public function execute($queue)
    {
        $postBody = [
            'responseArray'=>$this->responseArray,
            'nodeArray' => $this->nodeArray,
            'actionArray' => $this->actionArray
        ];
        Yii::error(VarDumper::export($postBody),__METHOD__);

        try {

            $this->processLndRpcEvent();

            //Last we register the action that LND has processed
            $nodeObject = LnNode::findOne($this->nodeArray['id']);
            $nodeObject->user->registerAction($this->actionArray['id'],$this->responseArray);

            //send to mongo
            if (getenv('MONGO_DB')) {
                $collection = Yii::$app->mongodb->getCollection($this->nodeArray['id'].'_'.$this->nodeArray['id']);
                $collection->insert($this->responseArray);
            }

        } catch (\Throwable $t) {
            \LNPay::error($t->getMessage(),__METHOD__);
        }


    }

    public function processLndRpcEvent()
    {
        switch ($this->actionArray['name']) {
            case 'Invoice': //Process "Invoice" RPC actions from the node
                $invoice = $this->responseArray;

                //Check for keysend payment
                try {
                    if (@$invoice['isKeysend'] && @$invoice['htlcs']) { //inbound keysend
                        $lnTx = LnTx::processKeysendInvoiceAction($invoice,LnNode::findOne($this->nodeArray['id']));
                        return $lnTx->toArray();
                    } else { // outbound keysend do nothing for now
                        //do not return false
                    }
                } catch (\Throwable $t) {
                    \LNPay::error('Processing keysend:'.$t->getMessage(),__METHOD__);
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