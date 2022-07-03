<?php


use lnpay\fixtures\UserFixture;

class WalletKeysendCest
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

    public function walleyKeysendSuccess(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'sak_KkKkKkKkKkneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet/wal_testCarolTransactions/keysend',[
            'dest_pubkey'=>'03d6ff489e59236894f7e8458727058a5a9eeb5ca03eded6a6814852f68f68aa0a',
            'num_satoshis'=>5,
            'passThru'=>'{"myData":"isHere"}',
            'custom_records'=>[1337331=>['Hi'=>'Test']]
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::CREATED);
        $I->seeResponseIsJson();
        $I->seeResponseContains('wtx_');
    }

    public function walleyKeysendFail(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'sak_KkKkKkKkKkneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet/wal_testCarolTransactions/keysend',[
            'dest_pubkey'=>'03d6ff489e59236894f7e8458727058a5a9eeb5ca03eded6a6814852f68f68aa0b', //bad pubkey
            'num_satoshis'=>5,
            'passThru'=>'{"myData":"isHere"}',
            //'custom_records'=>
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseContains('Failure reason: FAILURE_REASON_NO_ROUTE');
    }

    //@TODO: AMP sending / testing
    public function walletAMPSuccess(\ApiTester $I)
    {

    }
}
