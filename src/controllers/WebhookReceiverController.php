<?php
namespace lnpay\controllers;



use lnpay\models\LnTx;
use lnpay\node\models\LnNode;
use Yii;
use yii\web\Controller;


/**
 * Home controller
 */
class WebhookReceiverController extends Controller
{
    public $enableCsrfValidation = false;

    public function beforeAction($event)
    {
        return parent::beforeAction($event);
    }

    /**
     * {
    "responseObject": {
        "txHash": "c7c3ef2c45171178940c81b480a408d0ba01ceb63b59dab51b9ea17b17d8dc82",
        "amount": "111111",
        "numConfirmations": 1,
        "blockHash": "366d136232e62ea812d00dcae4a5c059e790d39c7c09fc11d11dfa125eebfdd1",
        "blockHeight": 379,
        "timeStamp": "1625789552",
        "destAddresses": [
        "bcrt1qk9vwckmfrxprgwvmz94xk2w4300u6sq2qczcth",
        "bcrt1qce35rurllu9t7aavt8nmdcu3e3ma2j3qpmfgdr"
        ],
        "rawTxHex": "020000000001010c43dfa07314572c145eda8b2c438bd063c3f12234329f7710221c787505d4e10100000000feffffff02c48dc89400000000160014b158ec5b69198234399b116a6b29d58bdfcd400a07b2010000000000160014c66341f07fff0abf77ac59e7b6e391cc77d54a200247304402200bb2f0ccc643786169b9d87af296d5553515d4f48c569a99636c852da2dd7247022049aaea0bb6cfe3fd84a6f4ea346fd7fcd9358a01c9cef694e2953f3bf923b7d0012102bbe5c5c21a6188e7d9a1061ae81d414ff063dbbc894e7d3116cd6b8944c5be227a010000"
    },
    "nodeObject": {
        "id": "lnod_z7jn8u5sdkv2hstpbd",
        "created_at": 1625783836,
        "user_label": null,
        "alias": "dave",
        "onchain_confirmed_sats": 964585,
        "onchain_unconfirmed_sats": 0,
        "onchain_total_sats": 964585,
        "onchain_nextaddr": "bcrt1q4hl00d807h6jd022uuwke6ukg95efmn6vhwcpw",
        "network": "regtest",
        "default_pubkey": "0220c660c75d207464663fc793cda493bc11ab91a42968afbe82349fe826bc4752",
        "uri": "0220c660c75d207464663fc793cda493bc11ab91a42968afbe82349fe826bc4752@192.168.69.1:9735",
        "host": "192.168.69.1",
        "rpc_port": 10004,
        "rest_port": 8084,
        "ln_port": 9735,
        "passThru": {
        "rest_last_check": 1625788875
        }
    },
    "actionObject": {
        "id": "GetTransactionsRequest_Transaction",
        "type": "lnd_rpc",
        "name": "Transaction",
        "display_name": "LND RPC Transaction"
        }
    }
     * @return array|false
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\ServerErrorHttpException
     */
    public function actionLnNodeIngestion()
    {
        $postBody = \LNPay::$app->request->getBodyParams();
        \LNPay::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (! ($nodeObject = LnNode::findOne(@$postBody['nodeObject']['id']))) {
            throw new \yii\web\ServerErrorHttpException(print_r($_REQUEST,TRUE).print_r($postBody,TRUE));
        }
    }



}