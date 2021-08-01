<?php


use lnpay\fixtures\UserFixture;

class NodeQueryRouteCest
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

    public function LndQueryRoute(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'sak_KkKkKkKkKkneieivTI05Fm3YzTza4N');
        $I->sendGET('/v1/node/lnod_bob/payments/queryroutes',[
            'pub_key'=>'025eb9588a5db262ebf195edb1a940428bac534e5b012b8b1d3011fdfa9f8f13db',
            'amt'=>1
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"routes"');
    }
}
