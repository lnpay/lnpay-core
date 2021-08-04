<?php


use lnpay\fixtures\UserFixture;

/**
 * @group base_api
 */
class AuthCest
{
    public function _fixtures()
    {
        return [
            'users' => [
                'class' => UserFixture::class,
            ],
            'user_access_keys' => [
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

    public function basicAuthFail(\ApiTester $I)
    {
        $I->amHttpAuthenticated('sak_fail', '');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/v1/user/view');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"name":"Unauthorized"');
    }

    public function basicAuthSuccess(\ApiTester $I)
    {
        $I->amHttpAuthenticated('sak_KkKkKkKkKkneieivTI05Fm3YzTza4N', '');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/v1/user/view');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"id":"usr_XIXkpKKKSJmDqW"');
    }

    public function queryParamAuthFail(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/v1/user/view?access-token=sak_fail');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"name":"Unauthorized"');
    }

    public function queryParamAuthSuccess(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/v1/user/view?access-token=sak_KkKkKkKkKkneieivTI05Fm3YzTza4N');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"id":"usr_XIXkpKKKSJmDqW"');
    }

    public function headerAuthFail(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'sak_fail');
        $I->sendGET('/v1/user/view');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"name":"Unauthorized"');
    }

    public function headerAuthSuccess(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'sak_KkKkKkKkKkneieivTI05Fm3YzTza4N');
        $I->sendGET('/v1/user/view');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"id":"usr_XIXkpKKKSJmDqW"');
    }
}
