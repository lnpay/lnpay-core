<?php

namespace tests\unit\models;

use app\behaviors\UserAccessKeyBehavior;
use app\components\HelperComponent;
use app\models\LnTx;
use app\models\wallet\LnWalletKeysendForm;
use app\models\wallet\LnWalletWithdrawForm;
use app\modules\node\models\LnNode;
use app\modules\node\models\NodeListener;
use app\models\wallet\Wallet;
use app\models\StatusType;
use app\models\User;
use Yii;

class LnNodeWalletTest extends \Codeception\Test\Unit
{
    public $tester;
    public $basePath;

    public $bobWallet=1111;
    public $aliceWallet=2222;

    protected function _before()
    {
        \Yii::$app->user->login(User::findIdentity(147));
        $this->bobWallet = Wallet::findOne($this->bobWallet);
        $this->aliceWallet = Wallet::findOne($this->aliceWallet);
    }
    /*
    public function testLnWalletKeysendForm()
    {
        expect_that($model = new LnWalletKeysendForm());
        expect_that($model->dest_pubkey = $this->bobWallet->lnNode->default_pubkey);
        expect_that($model->num_satoshis = 83);
        expect_that($model->custom_records = [696969=>'hello']);
        expect_that($model->wallet_id = 'wal_111111111111');
        expect_that($model->passThru = ['tim'=>2]);
        expect_that($r = $model->processKeysend());
        expect_that($r = $r->toArray());
        expect($r)->hasKey('lnTx');
        expect($r)->contains(-83);
    }*/


    public function testCreateInvoice()
    {
        expect_that($result = $this->bobWallet->generateLnInvoice(['num_satoshis'=>69]));
        expect($result)->isInstanceOf(LnTx::class);
        expect($result->num_satoshis)->equals(69);
    }

    public function testPayInvoice()
    {
        $bobBalance = $this->bobWallet->balance;
        expect_that($result = $this->aliceWallet->generateLnInvoice(['num_satoshis'=>69]));
        expect($result)->isInstanceOf(LnTx::class);
        expect($result->num_satoshis)->equals(69);

        expect_that($model = new LnWalletWithdrawForm());
        expect_that($model->wallet_id = 1111);
        expect_that($model->payment_request = $result->payment_request);
        expect($model->processWithdrawal())->isInstanceOf('app\models\wallet\WalletTransaction');
        $model->walletObject->releaseMutex();
        expect($model->walletObject->balance)->equals($bobBalance-69);

    }






}
