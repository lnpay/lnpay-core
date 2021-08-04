<?php


use lnpay\fixtures\UserFixture;

class WalletGenerateInvoiceCest
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

    public function walletGenerateInvoiceSuccess(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'sak_KkKkKkKkKkneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet/wal_KkMmfMmsksss/invoice',[
            'num_satoshis'=>200,
            'memo'=>'walletGenerateInvoiceSuccess',
            'passThru'=>'{"myObject":"tester"}'
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::CREATED);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"memo":"walletGenerateInvoiceSuccess (via LNPAY.co)"');
        $I->seeResponseContains('"num_satoshis":200');
        $I->seeResponseContains('"myObject":"tester"');
        $I->seeResponseContains('"lntx_');
    }

    public function walletGenerateInvoiceWakaSuccess(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet/waka_carol/invoice',[
            'num_satoshis'=>200,
            'memo'=>'walletGenerateInvoiceSuccess',
            'passThru'=>'{"myObject":"tester"}',
            'expiry'=>2000
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::CREATED);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"memo":"walletGenerateInvoiceSuccess (via LNPAY.co)"');
        $I->seeResponseContains('"num_satoshis":200');
        $I->seeResponseContains('"expiry":2000');
        $I->seeResponseContains('"myObject":"tester"');
        $I->seeResponseContains('"lntx_');
    }

    public function walletGenerateInvoiceWakiSuccess(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet/waki_carol/invoice',[
            'num_satoshis'=>200,
            'memo'=>'walletGenerateInvoiceSuccess',
            'passThru'=>'{"myObject":"tester"}'
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::CREATED);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"memo":"walletGenerateInvoiceSuccess (via LNPAY.co)"');
        $I->seeResponseContains('"num_satoshis":200');
        $I->seeResponseContains('"myObject":"tester"');
        $I->seeResponseContains('"lntx_');
    }

    public function walletGenerateInvoiceAccessKeyFail(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet/wal_KkMmfMmsksss/invoice',[
            'num_satoshis'=>200,
            'memo'=>'walletGenerateInvoiceFail',
            'passThru'=>'{"myObject":"tester"}'
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }

    public function walletGenerateInvoiceWakrFail(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet/wakr_carol/invoice',[
            'num_satoshis'=>200,
            'memo'=>'walletGenerateInvoiceFail',
            'passThru'=>'{"myObject":"tester"}'
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }

    public function walletGenerateInvoiceValidationFail(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'sak_KkKkKkKkKkneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet/wal_KkMmfMmsksss/invoice',[
            'num_satoshis'=>-20,
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::INTERNAL_SERVER_ERROR);
        $I->seeResponseIsJson();
        $I->seeResponseContains('Unable to create LN Invoice');
    }
}
