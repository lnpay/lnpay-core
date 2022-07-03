<?php


use lnpay\fixtures\UserFixture;

/**
 * @group base_api
 */
class WalletTransferCest
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
            ]
        ];
    }

    public function _before(ApiTester $I)
    {

    }

    public function _after(ApiTester $I)
    {

    }

    public function walletTransferSuccess(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'sak_KkKkKkKkKkneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet/wal_testCarolTransactions/transfer',[
            'dest_wallet_id'=>'wal_carolTransfer1',
            'lnPayParams'=>'{"myData":"isHere"}',
            'passThru'=>'{"myOtherData":"isHere"}',
            'num_satoshis'=>3
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::CREATED);
        $I->seeResponseIsJson();
        $I->seeResponseContains('wtx_transfer_in');
        $I->seeResponseContains('wtx_transfer_out');
        $I->seeResponseContains('"num_satoshis":3');
        $I->seeResponseContains('"num_satoshis":-3');
        $I->seeResponseContains('"myData":"isHere"');
        $I->seeResponseContains('"myOtherData":"isHere"');
    }

    public function walletTransferFailPak(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet/wal_testCarolTransactions/transfer',[
            'dest_wallet_id'=>'wal_carolTransfer1',
            'passThru'=>'"myData":"isHere"',
            'num_satoshis'=>3
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }

    public function walletTransferSuccessWaka(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet/waka_carolTransfer2/transfer',[
            'dest_wallet_id'=>'wal_carolTransfer1',
            'lnPayParams'=>'{"myData":"isHere"}',
            'passThru'=>'{"myOtherData":"isHere"}',
            'num_satoshis'=>3
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::CREATED);
        $I->seeResponseIsJson();
        $I->seeResponseContains('wtx_transfer_in');
        $I->seeResponseContains('wtx_transfer_out');
        $I->seeResponseContains('"num_satoshis":3');
        $I->seeResponseContains('"num_satoshis":-3');
        $I->seeResponseContains('"myData":"isHere"');
        $I->seeResponseContains('"myOtherData":"isHere"');
    }
    /*
     * THIS TEST IS COMMENTED OUT BECAUSE REFERENCING DEST_WALLET_ID WITH A KEY IS NOT SUPPORTED
    public function walletTransferSuccessWakaWaka(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet/waka_carolTransfer1/transfer',[
            'dest_wallet_id'=>'waka_carolTransfer1',
            'lnPayParams'=>'{"myData":"isHere"}',
            'passThru'=>'{"myOtherData":"isHere"}',
            'num_satoshis'=>3
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::CREATED);
        $I->seeResponseIsJson();
        $I->seeResponseContains('wtx_transfer_in');
        $I->seeResponseContains('wtx_transfer_out');
        $I->seeResponseContains('"num_satoshis":3');
        $I->seeResponseContains('"num_satoshis":-3');
        $I->seeResponseContains('"myData":"isHere"');
        $I->seeResponseContains('"myOtherData":"isHere"');
    }*/

    public function walletTransferFailWakr(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet/wakr_carolTransfer1/transfer',[
            'dest_wallet_id'=>'wal_carolTransfer1',
            'passThru'=>'"myData":"isHere"',
            'num_satoshis'=>3
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }

    public function walletTransferFailWaki(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet/waki_carolTransfer1/transfer',[
            'dest_wallet_id'=>'wal_carolTransfer1',
            'passThru'=>'"myData":"isHere"',
            'num_satoshis'=>3
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }

    public function walletTransferFailWaklw(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet/waklw_carolTransfer1/transfer',[
            'dest_wallet_id'=>'wal_carolTransfer1',
            'passThru'=>'"myData":"isHere"',
            'num_satoshis'=>3
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }


}
