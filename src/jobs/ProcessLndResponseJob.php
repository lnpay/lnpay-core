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
        \LNPay::info(json_encode($postBody),__METHOD__);

        try {

            $this->processLndRpcEvent();

            //Last we register the action that LND has processed
            $nodeObject = LnNode::findOne($this->nodeArray['id']);
            $nodeObject->user->registerAction($this->actionArray['id'],$this->responseArray);

            //send to mongo
            if (getenv('MONGO_DB')) {
                $collection = \LNPay::$app->mongodb->getCollection($this->nodeArray['id'].'_'.$this->nodeArray['id']);
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
                    if ( (@$invoice['isKeysend']) && @$invoice['htlcs']) { //inbound keysend
                        $lnTx = LnTx::processSpontaneousInvoiceAction($invoice,LnNode::findOne($this->nodeArray['id']));
                        return $lnTx->toArray();
                    } else if (@$invoice['isKeysend']) { // outbound keysend do nothing for now
                        return false;
                    }
                } catch (\Throwable $t) {
                    \LNPay::error('Error processing keysend:'.$t->getMessage(),__METHOD__);
                }

                //Check for AMP payment
                try {
                    if ( (@$invoice['isAmp']) && @$invoice['htlcs']) { //inbound AMP
                        $lnTx = LnTx::processSpontaneousInvoiceAction($invoice,LnNode::findOne($this->nodeArray['id']));
                        return $lnTx->toArray();
                    } else if (@$invoice['isAmp']) { // outbound AMP do nothing for now
                        return false;
                    }
                } catch (\Throwable $t) {
                    \LNPay::error('Error processing AMP:'.$t->getMessage(),__METHOD__);
                }


                //check for normal invoice payment
                try {
                    $lnTx = LnTx::processInvoiceAction($invoice);
                } catch (\Throwable $t) {
                    \LNPay::error('Error processing invoice action:'.$t->getMessage(),__METHOD__);
                }

                if ($lnTx instanceof LnTx) {
                    return $lnTx->toArray();
                }

                break;
        }
    }
}