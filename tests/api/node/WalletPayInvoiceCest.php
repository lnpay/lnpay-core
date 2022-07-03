<?php


use lnpay\fixtures\UserFixture;

class WalletPayInvoiceCest
{
    public function _fixtures()
    {
        return [
            'users' => [
                'class' => UserFixture::class,
            ],
            'wallets' => [
                'class' => \lnpay\fixtures\WalletFixture::class,
            ],
            'wallet_transactions' => [
                'class' => \lnpay\fixtures\WalletTransactionFixture::class,
            ],
            'user_access_key' => [
                'class' => \lnpay\fixtures\UserAccessKeyFixture::class,
            ],
            'ln_node' => [
                'class'=>\lnpay\node\fixtures\LnNodeFixture::class
            ]
        ];
    }

    public function _before(ApiTester $I)
    {

    }

    public function _after(ApiTester $I)
    {

    }

    public function generateTestInvoice()
    {
        expect_that($node = \lnpay\node\models\LnNode::findOne('lnod_bob'));
        expect_that($generated_invoice = (object) $node->getLndConnector('RPC')->createInvoice(['value'=>2]));

        return $generated_invoice;
    }

    public function walletPayInvoiceSuccess(\ApiTester $I)
    {
        $generated_invoice = $this->generateTestInvoice();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'sak_KkKkKkKkKkneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet/wal_testCarolTransactions/withdraw',[
            'payment_request'=>$generated_invoice->paymentRequest,
            'passThru'=>'{"myData":"isHere"}'
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::CREATED);
        $I->seeResponseIsJson();
        $I->seeResponseContains('wtx_');
        $I->seeResponseContains('lntx_');
        $I->seeResponseContains('"num_satoshis":2');
        $I->seeResponseContains('"myData":"isHere"');
    }

    public function walletPayInvoiceSuccessWaka(\ApiTester $I)
    {
        $generated_invoice = $this->generateTestInvoice();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet/waka_carolTransactions/withdraw',[
            'payment_request'=>$generated_invoice->paymentRequest
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::CREATED);
        $I->seeResponseIsJson();
        $I->seeResponseContains('wtx_');
        $I->seeResponseContains('lntx_');
        $I->seeResponseContains('"num_satoshis":2');
    }

    public function walletPayInvoiceSuccessWaklw(\ApiTester $I)
    {
        $generated_invoice = $this->generateTestInvoice();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet/waklw_carolTransactions/withdraw',[
            'payment_request'=>$generated_invoice->paymentRequest
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::CREATED);
        $I->seeResponseIsJson();
        $I->seeResponseContains('wtx_');
        $I->seeResponseContains('lntx_');
        $I->seeResponseContains('"num_satoshis":2');
    }

    public function walletPayInvoiceFailWaki(\ApiTester $I)
    {
        $generated_invoice = $this->generateTestInvoice();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet/waki_carolTransactions/withdraw',[
            'payment_request'=>$generated_invoice->paymentRequest
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }

    public function walletPayInvoiceFailWakr(\ApiTester $I)
    {
        $generated_invoice = $this->generateTestInvoice();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet/wakr_carolTransactions/withdraw',[
            'payment_request'=>$generated_invoice->paymentRequest
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }

    public function walletPayInvoiceInvalidPaymentRequestFail(\ApiTester $I)
    {
        $generated_invoice = $this->generateTestInvoice();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'sak_KkKkKkKkKkneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet/wal_testCarolTransactions/withdraw',[
            'payment_request'=>'lnbcaksdfk2km12k3m123'
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
    }

    public function walletPayInvoiceInsufficientFundsFail(\ApiTester $I)
    {
        $generated_invoice = $this->generateTestInvoice();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'sak_KkKkKkKkKkneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet/wal_KkMmfMmsksss/withdraw',[
            'payment_request'=>$generated_invoice->paymentRequest
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseContains('Invoice too large');
    }

    public function walletPayInvoiceValidationFail(\ApiTester $I)
    {
        $generated_invoice = $this->generateTestInvoice();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'sak_KkKkKkKkKkneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet/wal_KkMmfMmsksss/withdraw',[
            'payment_request'=>$generated_invoice->paymentRequest,
            'passThru'=>'asdf'
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseContains('passThru data must be valid json');
    }
}
