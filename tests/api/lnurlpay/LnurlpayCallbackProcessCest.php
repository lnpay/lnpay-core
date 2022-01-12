<?php



/**
 * @group base_api
 */
class LnurlpayCallbackProcessCest
{
    public function _fixtures()
    {
        return [
            'users' => [
                'class' => \lnpay\fixtures\UserFixture::class,
            ],
            'wallets' => [
                'class' => \lnpay\fixtures\WalletFixture::class,
            ],
            'lntx' => [
                'class' => \lnpay\fixtures\LnTxFixture::class,
            ],
            'user_access_key' => [
                'class' => \lnpay\fixtures\UserAccessKeyFixture::class,
            ],
            'wallet_lnurlpay' => [
                'class' => \lnpay\fixtures\WalletLnurlpayFixture::class,
            ]
        ];
    }

    public function _before(ApiTester $I)
    {
    }

    public function _after(ApiTester $I)
    {
    }

    //Test basic lnurl-pay processing
    public function getLnurlPayPublicProcessFailInvalidLnurlpId(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/v1/wallet/waklp_aliceLnurlPay/lnurlp/lnurlp_thisisinvalid');
        $I->seeResponseIsJson();
        $I->seeResponseContains('"reason":"Wallet or lnurlpay link is not valid or active"');
    }

    //Test basic lnurl-pay processing
    public function getLnurlPayPublicProcessFailInactiveLnurlpId(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/v1/wallet/waklp_aliceLnurlPay/lnurlp/lnurlp_thisisinactive');
        $I->seeResponseIsJson();
        $I->seeResponseContains('"reason":"Wallet or lnurlpay link is not valid or active"');
    }

    public function getLnurlPayPublicProcessFailMismatchWalletAndLnurlpayLink(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/v1/wallet/waklw_aliceLnurlWithdraw/lnurlp/lnurlp_0YM18Nt3po8SUmIOKE');
        $I->seeResponseIsJson();
        $I->seeResponseContains('"reason":"Wallet or lnurlpay link is not valid or active"');
    }

    public function getLnurlPayPublicProcessFailWalletNotfound(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/v1/wallet/waklp_aliceLnurlWithdraw/lnurlp/lnurlp_0YM18Nt3po8SUmIOKE');
        $I->seeResponseIsJson();
        $I->seeResponseContains('"reason":"Wallet not found');
    }

    public function getLnurlPayPublicProcessSucceed(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/v1/wallet/waklp_aliceLnurlPay/lnurlp/lnurlp_0YM18Nt3po8SUmIOKE');
        $I->seeResponseIsJson();
        $I->seeResponseContains('"minSendable":1000');
    }

    //test callback where invoice is generated
    public function getLnurlPayPublicCallbackFailInvalidSatoshiAmount(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/v1/wallet/waklp_aliceLnurlPay/lnurlp/lnurlp_0YM18Nt3po8SUmIOKE?amount=10');
        $I->seeResponseIsJson();
        $I->seeResponseContains('sat is not within');
    }
    /*
    public function getLnurlPayPublicCallbackSucceed(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/v1/wallet/waklp_aliceLnurlPay/lnurlp/lnurlp_0YM18Nt3po8SUmIOKE?amount=1000');
        $I->seeResponseIsJson();
        $I->seeResponseContains('"pr":');
    }*/
}
