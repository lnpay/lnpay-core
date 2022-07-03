<?php


use lnpay\fixtures\UserFixture;

/**
 * @group base_api
 */
class PermanentLnurlWithdrawCest
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
            'lntx' => [
                'class' => \lnpay\fixtures\LnTxFixture::class,
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

    public function getDisposableLnurlWithdrawSuccessSak(\ApiTester $I)
    {
        $arbData = base64_encode('{"myData":"isEncoded"}');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'sak_KkKkKkKkKkneieivTI05Fm3YzTza4N');
        $I->sendGET('/v1/wallet/wal_aliceLnurlWithdraw/lnurl/withdraw-static?memo=Tester&num_satoshis=69&passThru='.$arbData);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"lnurl":"LNURL');
        $lnurl = $I->grabDataFromResponseByJsonPath('$.lnurl');
        expect_that($decodedLnurl = tkijewski\lnurl\decodeUrl($lnurl[0]));
        codecept_debug($decodedLnurl);
        expect($decodedLnurl)->hasKey('url');
        expect($decodedLnurl['url'])->startsWith('http://localhost/index-test.php/v1/wallet/waklw_aliceLnurlWithdraw/lnurl-process?');

        expect($decodedLnurl)->hasKey('tag');
        expect($decodedLnurl)->contains('withdraw');

        expect($decodedLnurl)->hasKey('memo');
        expect($decodedLnurl)->contains('Tester');

        expect($decodedLnurl)->hasKey('passThru');
        expect($decodedLnurl)->contains($arbData);

        expect($decodedLnurl)->hasKey('num_satoshis');
        expect($decodedLnurl)->contains('69');
    }

    public function getDisposableLnurlWithdrawSuccessPak(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendGET('/v1/wallet/waklw_aliceLnurlWithdraw/lnurl/withdraw-static');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }

    public function getDisposableLnurlWithdrawFailWakiPak(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendGET('/v1/wallet/waki_aliceLnurlWithdraw/lnurl/withdraw-static');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }

    public function getDisposableLnurlWithdrawFailWakrPak(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendGET('/v1/wallet/wakr_aliceLnurlWithdraw/lnurl/withdraw-static');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }

    public function getDisposableLnurlWithdrawSuccessWakaPak(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendGET('/v1/wallet/waka_aliceLnurlWithdraw/lnurl/withdraw-static');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"lnurl":"LNURL');
    }
}
