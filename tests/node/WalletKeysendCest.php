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

    // I CANNOT GET THIS TO WORK IN A DEV ENVIRONMENT, TF
    /*public function walleyKeysendSuccess(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'sak_KkKkKkKkKkneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet/wal_testCarolTransactions/keysend',[
            'dest_pubkey'=>'037ab245cff7f54f76e605327d93e18ed641bddd49e3e46dafb13a5a646c25a041',
            'num_satoshis'=>5,
            'passThru'=>'{"myData":"isHere"}',
            //'custom_records'=>
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::CREATED);
        $I->seeResponseIsJson();
        $I->seeResponseContains('wtx_');
    }*/
}
