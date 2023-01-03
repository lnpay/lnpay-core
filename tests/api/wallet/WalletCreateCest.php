<?php


use lnpay\fixtures\UserFixture;

/**
 * @group base_api
 */
class WalletCreateCest
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

    public function walletCreateSuccess(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'sak_KkKkKkKkKkneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet',[
            'user_label'=>'My Test Wallet'
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::CREATED);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"access_keys"');
        $I->seeResponseContains('"user_label":"My Test Wallet"');
        $I->seeResponseContains('"default_lnurlpay_id"');
    }

    public function walletCreateDeterministicSuccess(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'sak_KkKkKkKkKkneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet',[
            'user_label'=>'My Test Deterministic Wallet',
            'deterministic_identifier'=>'my_id'
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::CREATED);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"access_keys"');
        $I->seeResponseContains('"user_label":"My Test Deterministic Wallet"');
        $I->seeResponseContains('"default_lnurlpay_id"');
        $I->seeResponseContains('walx_'.substr(hash('sha256',('my_id'.'org_1234567')),0,14));
    }

    public function walletCreateSuccessNotCustodialNode(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'sak_KkKkKkKkKkneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet',[
            'user_label'=>'My Test Wallet',
            'ln_node_id'=>'lnod_bob'
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::CREATED);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"access_keys"');
        $I->seeResponseContains('"user_label":"My Test Wallet"');
    }

    public function walletCreateValidationFail(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'pak_HgiUO4kskfneieivTI05Fm3YzTza4N');
        $I->sendPOST('/v1/wallet',[]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
    }

    public function walletCreatePermissionFail(\ApiTester $I)
    {
        //Only sak_ can do this command
    }

}
