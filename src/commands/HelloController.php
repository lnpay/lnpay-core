<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace lnpay\commands;

use lnpay\components\HelperComponent;
use lnpay\components\MailerComponent;
use lnpay\node\components\LndNodeConnector;
use lnpay\node\components\LnMacaroonObject;
use lnpay\models\LnTx;
use lnpay\wallet\models\LnWalletKeysendForm;
use lnpay\wallet\models\WalletTransferForm;
use lnpay\node\models\LnCluster;
use lnpay\node\models\LnNode;
use lnpay\wallet\models\LnWalletWithdrawForm;
use lnpay\wallet\models\Wallet;
use lnpay\wallet\models\WalletTransaction;
use lnpay\wallet\models\WalletTransactionType;
use BitcoinPHP\BitcoinECDSA\BitcoinECDSA;
use Codeception\Lib\Generator\Helper;
use Google\Rpc\Help;
use kornrunner\Secp256k1;
use kornrunner\Serializer\HexPrivateKeySerializer;
use kornrunner\Serializer\HexSignatureSerializer;
use Lnrpc\GenSeedRequest;
use Lnrpc\GetInfoRequest;
use Mdanter\Ecc\Curves\CurveFactory;
use Mdanter\Ecc\Curves\SecgCurve;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Serializer\PrivateKey\DerPrivateKeySerializer;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use Routerrpc\SendPaymentRequest;
use yii\console\Controller;
use yii\console\ExitCode;
use Yii;
use yii\db\Expression;
use yii\helpers\VarDumper;
use yii\web\ServerErrorHttpException;


/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class HelloController extends Controller
{
    public function actionVerifyWalletBalance($id)
    {
        $wallet = Wallet::findOne($id);
        echo "Wallet Transaction Calc balance: ".$wallet->calculateBalance();
        echo "\n";
        echo "Wallet Balance in wallet table:  ".$wallet->balance;
        echo "\n";
    }
    public function actionCompressWallet($id)
    {
        $wallet = Wallet::findOne($id);
        print_r($wallet->compressTransactions());
    }
    public function actionLp()
    {
        $w = Wallet::find()->one();
        $p = $w->generateLnurlpay();
        echo VarDumper::export($p);
    }
    public function actionSsh()
    {
        $nodeId = 'testernode';
        $clu = LnCluster::find()->one();
        print_r($clu->getNextAvailablePorts());
        $ssh = $clu->getSshConnection();

        /* ubuntu is the username used by amazon ec2 */
        $parameters = [];
        $config_file = $clu->generateConfigFileForSubnode($parameters);
        echo $ssh->exec('echo "'.$config_file.'" > '.$clu->clusterDataPath.$nodeId.'/lnd.conf');
    }

    public function actionT()
    {
        $model = new LnWalletWithdrawForm();
        $model->payment_request = 'lnbcrt20n1p03g7kapp5cjudz84qrc52hzqq7menvgh2m9kk20d5nh0s39gh9dvjtxlx9wrsdqqcqzpgsp5jlddtl0zznlmxlvxgcv58ctn9t7qftlmfvm5ny3hlttj433nfdfs9qy9qsqcm0xprj42v4clallnux5jgrvmena4ntvxjdq96dud5ea7cjfep7yg08asz4vtgcc59h0a4fq89w6llldp6kw0zw8jmvnt7qqacx0kuqp8gnnp3';
        $model->wallet_id = 6;
        $model->processWithdrawal([]);

    }
    public function actionTransfer($s_w,$d_w,$sat)
    {
        $t = new WalletTransferForm();
        $t->source_wallet_id = $s_w;
        $t->dest_wallet_id = $d_w;
        $t->num_satoshis = $sat;
        if ($t->validate()) {
            $t->executeTransfer();
        } else {
            echo HelperComponent::getFirstErrorFromFailedValidation($t);
        }

    }

    public function actionSettle($id,$sats,$preimage)
    {
        $lnTx = LnTx::find()->where(['payment_request'=>$id])->one();
        $lnTx->settled = 1;
        $lnTx->num_satoshis = $sats;
        $lnTx->payment_preimage = $preimage;
        $lnTx->settled_at = time();
        if (!$lnTx->save()) {
            echo HelperComponent::getFirstErrorFromFailedValidation($lnTx);
        }
    }


    public function actionMac()
    {
        $raw = 'AgEEbHNhdAJCAACZgChA0/HX4cotoVmUyfutAN7Uhf77QvXJ1teO6LYrOaiYe/FUoXJL5g4vz/1Z5TENdsM+oVzyMm4jF1NSciUkAAITc2VydmljZXM9Ym9zc2NvcmU6MAACFmJvc3Njb3JlX2NhcGFiaWxpdGllcz0AAAYgclmXQYsaZFlLQvKVKgxuYblx0s7Yskkn2w5z0UM5LPw=';

        $r = HelperComponent::base64url_encode(base64_decode($raw));
        //echo $r;

        $m = new LnMacaroonObject($r);
    }

    public function actionBalance()
    {
        $node = LnNode::findOne('lnod_alice');
        $rpcConnector = LndNodeConnector::initConnectorRpc($node,LndNodeConnector::SERVICE_LIGHTNING);

        $r = new \Lnrpc\WalletBalanceRequest();
        $resp = $rpcConnector->WalletBalance($r);
        $x = $resp->wait();
        //print_r($x[0]->serializeToJsonString());exit;
        echo 'Total balance: '.$x[0]->getTotalBalance();
        echo "\n";
        echo 'Confirm balance: '.$x[0]->getConfirmedBalance();
        echo "\n";
        echo 'Unconfirm balance: '.$x[0]->getUnconfirmedBalance();
        echo "\n";
    }

    public function actionNextaddr()
    {
        $node = \lnpay\node\models\LnNode::findOne('lnod_zpn99ptme8ss9t');
        echo $node->id;exit;
        $rpcConnector = LndNodeConnector::initConnectorRpc($node,LndNodeConnector::SERVICE_LIGHTNING);

        $r = new \Lnrpc\NewAddressRequest();
        $resp = $rpcConnector->NewAddress($r);
        $x = $resp->wait();
        print_r($x[0]->serializeToJsonString());

    }

    public function actionKeysendPlay()
    {
        $model = new LnWalletKeysendForm();
        $model->dest_pubkey = '03f84bcd167b6989529815c3b6d5826ae3b61f47a920ef1521f1501951db4ab39f';
        $model->num_satoshis = 1;
        $model->custom_records = [696969=>'hello'];
        $model->wallet_id = 'wal_ZlszkwrH8ZdYGQ';
        $model->passThru = ['tim'=>2];
        $r = $model->processKeysend();

        echo VarDumper::export($r);
        exit;
    }


    public function actionBake()
    {
        $con = LndNodeConnector::initConnectorRpc(LnNode::findOne('lnod_jQzFay1d'));

        $perms = [];
        foreach (LnMacaroonObject::getAllowedPermissionMap() as $entity => $actions) {
            foreach ($actions as $a) {
                $p = new \Lnrpc\MacaroonPermission();
                $p->setEntity($entity);
                $p->setAction($a);
                $perms[] = $p;
            }
        }


        $sub = new \Lnrpc\BakeMacaroonRequest();
        $sub->setPermissions($perms);
        $result = $con->BakeMacaroon($sub)->wait();
        //VarDumper::dump($result);
        echo $result[0]->getMacaroon();
        echo "\n";
    }

    public function actionDeletePayments()
    {
        $node = LnNode::findOne('lnod_2s4yfYA');
        echo $node->getLndConnector()->deleteAllPayments();
    }

    public function actionBitcoinsig($domain,$k1)
    {
        $bitcoinECDSA = new BitcoinECDSA();
        $walletPrivateKey = 'walletPrivateKey';
        $walletPrivateKeyBasedOnDomain = hash_hmac('sha256',$domain,$walletPrivateKey);
        $bitcoinECDSA->setPrivateKey($walletPrivateKeyBasedOnDomain);
        echo "priv: ".$bitcoinECDSA->getPrivateKey();
        echo "\n";
        echo "pub : ".$pubKey=$bitcoinECDSA->getPubKey();
        echo "\n";
        echo "sig : ".$sig=$bitcoinECDSA->signHash(bin2hex($k1));
        echo "\n";
        echo 'checksig:'.$bitcoinECDSA->checkDerSignature($pubKey, $sig, bin2hex($k1));
        echo "\n";
    }
}
