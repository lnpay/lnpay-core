<?php

namespace tests\unit\models;

use lnpay\fixtures\WalletTransactionFixture;
use lnpay\models\BaseLink;
use lnpay\models\integration\IntegrationWebhook;
use lnpay\models\LnTx;
use lnpay\models\StatusType;
use lnpay\models\User;
use lnpay\fixtures\UserAccessKeyFixture;
use lnpay\fixtures\UserFixture;
use lnpay\wallet\models\Wallet;
use lnpay\wallet\models\WalletTransaction;
use Yii;

class WalletCompressTest extends \Codeception\Test\Unit
{
    public $tester;

    public function _fixtures()
    {
        return [
            'wallet_transactions' => [
                'class' => WalletTransactionFixture::class,
            ]
        ];
    }

    protected function _before()
    {

    }

    public function testWalletCompress()
    {
        expect_that($w = Wallet::findOne(12));
        expect($w->compressTransactions())->equals([
            'balance'=>100,
            'sum'=>100
        ]);
        expect(WalletTransaction::find()->where(['wallet_id'=>12])->count())->equals(1);
    }
}
