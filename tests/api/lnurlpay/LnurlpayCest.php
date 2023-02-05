<?php



/**
 * @group base_api
 */
class LnurlpayCest
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
            'custy_domain' => [
                'class' => \lnpay\fixtures\CustyDomainFixture::class,
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

    public function getLnAddressProbeSuccess(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendGET('/v1/lnurlp/probe/lnurlp_0YM18Nt3po8SUmIOKE@localhost');
        $I->seeResponseIsJson();
        $I->seeResponseContains('"metadata"');
        $I->seeResponseContains('"maxSendable"');
    }

    public function getLnAddressProbeFail(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendGET('/v1/lnurlp/probe/lnurlp_0YM18Nt3po8INVALID@localhost');
        $I->seeResponseCodeIs(500);
    }

    public function getLnurlpayProbeSuccess(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendGET('/v1/lnurlp/probe/lnurl1dp68gup69uhnzwfj9ccnvwpwxcujuvf39a5kuer90qkhgetnwsh8q6rs9amrztmhv9kxcet59amkz6mvwp0kzmrfvdj5cmn4wfk9qcte9akxuatjd3cz7mrww4excuzlxpv56vfcfe6rxur08pf42m2ffa9525qq4ry');
        $I->seeResponseIsJson();
        $I->seeResponseContains('"metadata"');
        $I->seeResponseContains('"maxSendable"');
    }

    public function getViewLnurlpayId(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendGET('/v1/lnurlp/lnurlp_0YM18Nt3po8SUmIOKE');
        $I->seeResponseIsJson();
        $I->seeResponseContains('"lnurlp_0YM18Nt3po8SUmIOKE"');
    }

    public function getLnurlpayProbeFail(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendGET('/v1/lnurlp/probe/LNUR1DP68GUP69UHNZWFJ9CCNVWPWXCUJUVF39AMRZTMHV9KXCET59AMKZ6MVWP0K54EHWEPHQSNEFF6NZ6JTW4R9S4PN2454Y7JZXCHKCMN4WFK8QTMVDE6HYMRSTAENJ6MD2CMNYDZCW3D8VENCVD54S3SH8JMLX');
        $I->seeResponseIsJson();
        $I->seeResponseContains('Exception');

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendGET('/v1/lnurlp/probe/LNURL1DP68GUP69UHKCMNSV9UJUMR0VDSKCW3CXYCNZTMKXYHHWCTVD3JHGTMHV94KCUZLD439Y36XG4952ARFWD557AMSGFXXY6R4VFPY5AP0D3H82UNVWQHKCMN4WFK8QH60FPNYXJ250FZHSJJPGET5G7ZKW4HS4PHG32');
        $I->seeResponseIsJson();
        $I->seeResponseContains('Exception');
    }

    public function payLnurlpayFailWithdrawPermission(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet/waklp_aliceLnurlPay/lnurlp/pay',[
            'amt_msat'=>100000,
            'lnurlpay_encoded'=>'lnurl1dp68gup69uhnzwfj9ccnvwpwxcujuvf39a5kuer90qkhgetnwsh8q6rs9amrztmhv9kxcet59amkz6mvwp0kzmrfvdj5cmn4wfk9qcte9akxuatjd3cz7mrww4excuzlxpv56vfcfe6rxur08pf42m2ffa9525qq4ry'
        ]);
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(401);
    }

    public function payLnurlpayFailAmountBelowMin(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet/waklw_aliceLnurlWithdraw/lnurlp/pay',[
            'amt_msat'=>100000000,
            'lnurlpay_encoded'=>'lnurl1dp68gup69uhnzwfj9ccnvwpwxcujuvf39a5kuer90qkhgetnwsh8q6rs9amrztmhv9kxcet59amkz6mvwp0kzmrfvdj5cmn4wfk9qcte9akxuatjd3cz7mrww4excuzlxpv56vfcfe6rxur08pf42m2ffa9525qq4ry'
        ]);
        $I->seeResponseIsJson();
        $I->seeResponseContains('cannot accept more');
    }

    public function payLnurlpayFailAmountAboveMax(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet/waklw_aliceLnurlWithdraw/lnurlp/pay',[
            'amt_msat'=>122,
            'lnurlpay_encoded'=>'lnurl1dp68gup69uhnzwfj9ccnvwpwxcujuvf39a5kuer90qkhgetnwsh8q6rs9amrztmhv9kxcet59amkz6mvwp0kzmrfvdj5cmn4wfk9qcte9akxuatjd3cz7mrww4excuzlxpv56vfcfe6rxur08pf42m2ffa9525qq4ry'
        ]);
        $I->seeResponseIsJson();
        $I->seeResponseContains('cannot accept less');
    }

    public function payLnurlpayCommentTooLong(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet/waklw_aliceLnurlWithdraw/lnurlp/pay',[
            'amt_msat'=>1202,
            'lnurlpay_encoded'=>'lnurl1dp68gup69uhnzwfj9ccnvwpwxcujuvf39a5kuer90qkhgetnwsh8q6rs9amrztmhv9kxcet59amkz6mvwp0kzmrfvdj5cmn4wfk9qcte9akxuatjd3cz7mrww4excuzlvdhk6mt9de6yzmrvdamk2eq44lz39',
            'comment'=>'commentcommentcomment'
        ]);
        $I->seeResponseIsJson();
        $I->seeResponseContains('Comment length is too long');
    }

    public function CreateLnurlPaySuccess(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet/waka_aliceLnurlPay/lnurlp',[
            'lnurlp_minSendable_msat'=>1202,
            'lnurlp_maxSendable_msat'=>10000,
            'identifier'=>'username'
        ]);
        $I->seeResponseIsJson();
        $I->seeResponseContains('wallet_id');
    }

    public function UpdateLnurlpaySuccessDomainName(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet/waka_aliceLnurlPay/lnurlp',[
            'custy_domain_id'=>'cdom_123'
        ]);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"domain_name":"lnpay.local"');
    }

    public function UpdateLnurlpaySuccessDomainNull(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet/waka_aliceLnurlPay/lnurlp',[
            'custy_domain_id'=>NULL
        ]);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"custyDomain":null');
    }

    public function UpdateLnurlpayFailDomainName(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet/waka_aliceLnurlPay/lnurlp',[
            'custy_domain_id'=>6
        ]);
        $I->seeResponseCodeIs(400);
    }


    public function CreateLnurlPayFail(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet/waka_aliceLnurlPay/lnurlp',[
            'lnurlp_minSendable_msat'=>11,
            'lnurlp_maxSendable_msat'=>10000,
            'identifier'=>'username',
        ]);
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(400);
    }

    public function LnurlpayCustyDomainNameRedirect(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/.well-known/cdom_123/lnurlp/testerUser');
        $I->seeResponseIsJson();
        $I->seeResponseContains('"minSendable":69000');
    }


}
