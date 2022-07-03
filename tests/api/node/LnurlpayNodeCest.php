<?php




class LnurlpayNodeCest
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
            ],
            'wallet_transaction' => [
                'class' => \lnpay\fixtures\WalletTransactionFixture::class,
            ]
        ];
    }

    public function _before(ApiTester $I)
    {
    }

    public function _after(ApiTester $I)
    {
    }


    public function payLnurlpaySucceedWithdrawPermission(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet/waklw_aliceLnurlWithdraw/lnurlp/pay',[
            'amt_msat'=>1000,
            'lnurlpay_encoded'=>'lnurl1dp68gup69uhnzwfj9ccnvwpwxcujuvf39a5kuer90qkhgetnwsh8q6rs9amrztmhv9kxcet59amkz6mvwp0kzmrfvdj5cmn4wfk9qcte9akxuatjd3cz7mrww4excuzlxpv56vfcfe6rxur08pf42m2ffa9525qq4ry',
            'passThru'=>['dog'=>'cat']
        ]);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"ln_lnurl_pay_outbound"');
        $I->seeResponseContains('"dog"');
    }

    public function payLnaddressSucceedWithdrawPermission(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet/waklw_aliceLnurlWithdraw/lnurlp/pay',[
            'amt_msat'=>1000,
            'ln_address'=>'lnurlp_0YM18Nt3po8SUmIOKE@localhost.com',
            'passThru'=>['dog'=>'cat']
        ]);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"ln_lnurl_pay_outbound"');
        $I->seeResponseContains('"dog"');
    }
}
