<?php


use lnpay\fixtures\UserFixture;

/**
 * @group base_api
 */
class WalletGetCest
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
            ]
        ];
    }

    public function _before(ApiTester $I)
    {
    }

    public function _after(ApiTester $I)
    {
    }

    public function walletGetSuccessById(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'sak_KkKkKkKkKkneieivTI05Fm3YzTza4N');
        $I->sendGET('/v1/wallet/wal_KkMmfMmsksss');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"id":"wal_KkMmfMmsksss"');
    }

    public function walletGetFailByPakId(\ApiTester $I)
    {
        //pak_ cannot access the wallet by it's ID only by wakX key
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendGET('/v1/wallet/wal_KkMmfMmsksss');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }

    public function walletGetSuccessByAccessKeyWakr(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendGET('/v1/wallet/wakr_alice');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"id":"wal_KkMmfMmsksss"');
    }

    public function walletGetSuccessByAccessKeyWaka(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendGET('/v1/wallet/waka_alice');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"id":"wal_KkMmfMmsksss"');
    }

    public function walletGetSuccessByAccessKeyWaklw(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendGET('/v1/wallet/waklw_alice');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"id":"wal_KkMmfMmsksss"');
    }


    public function walletGetFailByUnknownId(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'sak_KkKkKkKkKkneieivTI05Fm3YzTza4N');
        $I->sendGET('/v1/wallet/unknown_id');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }

    public function walletGetFailByAccessKeyWaki(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendGET('/v1/wallet/waki_alice');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }
}
