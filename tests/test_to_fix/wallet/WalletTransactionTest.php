<?php

namespace tests\unit\models;

use lnpay\behaviors\UserAccessKeyBehavior;
use lnpay\wallet\models\LnWalletWithdrawForm;
use lnpay\wallet\models\Wallet;
use lnpay\models\StatusType;
use lnpay\models\User;
use lnpay\wallet\models\WalletTransaction;
use lnpay\node\models\LnNode;
use Yii;

class WalletTransactionTest extends \Codeception\Test\Unit
{
    public $tester;
    public $bobNode;
    public $aliceNode;
    public $carolNode;

    protected function _before()
    {
        \LNPay::$app->user->login(User::findIdentity(147));
        $this->bobNode = LnNode::findOne('lnod_bob');
        $this->aliceNode = LnNode::findOne('lnod_alice');
        $this->carolNode = LnNode::findOne('lnod_carol');
    }

    //@TODO: this test
    public function testDetermineWtxType()
    {

    }

    public function testCreateNetworkFeeTransactionNoFee()
    {
        $r = $this->carolNode->getLndConnector()->createInvoice(['value'=>10]);
        $form = new LnWalletWithdrawForm();
        $form->payment_request = $r['payment_request'];
        $form->wallet_id = 2222; // alice wallet
        expect($wtx = $form->processWithdrawal());
        $resultTx = $wtx->createNetworkFeeTransaction();
        expect($resultTx)->isEmpty(); //should be null because there is no fee to process
        $form->walletObject->releaseMutex();
    }

    public function testCreateNetworkFeeTransaction()
    {
        $r = $this->carolNode->getLndConnector()->createInvoice(['value'=>10]);
        $form = new LnWalletWithdrawForm();
        $form->payment_request = $r['payment_request'];
        $form->wallet_id = 1111; // bob wallet
        $form->walletObject->releaseMutex();
        expect($wtx = $form->processWithdrawal());
        $resultTx = $wtx->createNetworkFeeTransaction();
        expect($resultTx)->isInstanceOf(WalletTransaction::class);

        expect($resultTx->num_satoshis)->equals((int) ceil($wtx->lnTx->fee_msat/1000)*-1);
    }

    public function testCreateServiceFeeTransaction()
    {
        $r = $this->bobNode->getLndConnector()->createInvoice(['value'=>10]);
        $form = new LnWalletWithdrawForm();
        $form->payment_request = $r['payment_request'];
        $form->wallet_id = 1211; // carol wallet
        expect($wtx = $form->processWithdrawal());
        $resultTx = $wtx->createServiceFeeTransaction();
        $form->walletObject->releaseMutex();
        expect($resultTx)->isEmpty(); //this user does not have any fees set
    }
}
