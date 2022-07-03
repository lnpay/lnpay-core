<?php

namespace tests\unit\models;

use lnpay\components\HelperComponent;
use lnpay\wallet\models\LnWalletWithdrawForm;
use lnpay\models\User;
use lnpay\wallet\models\WalletTransferForm;
use Yii;

class WalletTransferFormTest extends \Codeception\Test\Unit
{
    public $tester;

    protected function _before()
    {
        \LNPay::$app->user->login(User::findIdentity(147));
    }

    public function testInvalidWalletIds()
    {
        expect_that($model = new WalletTransferForm());
        expect_that($model->source_wallet_id = 'wa_invalid');
        expect_that($model->dest_wallet_id = 'LWAcasdfOt6i93WFh');
        expect($model->validate())->false();
        expect(HelperComponent::getFirstErrorFromFailedValidation($model))->equals("Invalid source wallet id");

        expect_that($model = new WalletTransferForm());
        expect_that($model->source_wallet_id = 'LWAcasdfOt6i93WFh');
        expect_that($model->dest_wallet_id = 'wa_invalid');
        expect($model->validate())->false();
        expect(HelperComponent::getFirstErrorFromFailedValidation($model))->equals("Invalid dest wallet id");
    }

    public function testDifferentWallets()
    {
        expect_that($model = new WalletTransferForm());
        expect_that($model->source_wallet_id = 'w_asdfm3krm2k3mr23');
        expect_that($model->dest_wallet_id = 'w_asdfm3krm2k3mr23');
        expect($model->validate())->false();
        expect(HelperComponent::getFirstErrorFromFailedValidation($model))->equals("Source and destination wallets cannot be the same!");
    }

    public function testInsufficientBalance()
    {
        expect_that($model = new WalletTransferForm());
        expect_that($model->num_satoshis = 200);
        expect_that($model->source_wallet_id = 'wal_LWAcZwCeDsezSfFe');
        expect_that($model->dest_wallet_id = 'LWAcasdfOt6i93WFh');
        expect($model->validate())->false();
        expect(HelperComponent::getFirstErrorFromFailedValidation($model))->equals("Insufficient balance in source wallet");
    }

    public function testInvalidJson()
    {
        expect_that($model = new WalletTransferForm());
        expect_that($model->num_satoshis = 1);
        expect_that($model->source_wallet_id = 'wal_LWAcZwCeDsezSfFe');
        expect_that($model->dest_wallet_id = 'LWAcasdfOt6i93WFh');
        expect_that($model->lnPayParams = 'asd');
        expect($model->validate())->false();
        expect(HelperComponent::getFirstErrorFromFailedValidation($model))->equals("Invalid lnPayParams json specified");
    }

    public function testWalletTransferSuccess()
    {
        expect_that($model = new WalletTransferForm());
        expect_that($model->num_satoshis = 22);
        expect_that($model->source_wallet_id = 'wal_LWAcZwCeDsezSfFe');
        expect_that($model->dest_wallet_id = 'LWAcasdfOt6i93WFh');
        expect($model->validate())->true();
        expect_that($result = $model->executeTransfer());
        expect($result)->hasKey('wtx_transfer_in');
        expect($result)->hasKey('wtx_transfer_out');
    }




}
