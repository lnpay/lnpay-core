<?php

namespace tests\unit\models;

use lnpay\models\action\ActionFeed;
use lnpay\models\action\ActionName;
use lnpay\models\User;
use lnpay\fixtures\UserAccessKeyFixture;
use lnpay\fixtures\UserFixture;

class UserTest extends \Codeception\Test\Unit
{
    public function _fixtures()
    {
        return [
            'users' => [
                'class' => UserFixture::class,
            ],
            'user_access_keys' => [
                'class' => UserAccessKeyFixture::class,
            ],
        ];
    }

    public function testFindUserById()
    {
        expect_that($user = User::findIdentity(147));
        expect($user->username)->equals('bandit');

        expect_not(User::findIdentity(999));
    }

    public function testFindUserByAccessToken()
    {
        expect_that($user = User::findIdentityByAccessToken('THMbv7j1m-d3spjMUW0IAcSa281MeS1N'));
        expect($user->username)->equals('bandit');

        expect_not(User::findIdentityByAccessToken('non-existing'));        
    }

    public function testFindUserByUsername()
    {
        expect_that($user = User::findByUsername('bandit'));
        expect_not(User::findByUsername('not-admin'));
    }

    public function testValidateUser()
    {
        $user = User::findByUsername('bandit');
        expect_that($user->validateAuthKey('K3nF70it7tzNsHddEiq0BZ0i-OU8S3xV'));
        expect_not($user->validateAuthKey('test102key'));

        expect_that($user->validatePassword('123456'));
        expect_not($user->validatePassword('notpass'));
    }

    public function testGetJsonData()
    {
        $user = User::findByUsername('jsonTester');

        expect($user->getJsonData($user::DATA_LNURL_OTT))->equals('tester-ott');
        expect($user->getJsonData())->equals([$user::DATA_LNURL_OTT=>'tester-ott']);
    }

    public function testAppendJsonData()
    {
        $user = User::findByUsername('jsonTester');

        expect($user->appendJsonData(['append'=>'lenoir']))->equals([$user::DATA_LNURL_OTT=>'tester-ott','append'=>'lenoir']);

        expect($user->getJsonData('append'))->equals('lenoir');
        expect($user->getJsonData())->equals([$user::DATA_LNURL_OTT=>'tester-ott','append'=>'lenoir']);
    }

    public function testDeleteJsonData()
    {
        $user = User::findByUsername('jsonTester');

        $user->appendJsonData(['append'=>'lenoir']);

        expect($user->deleteJsonData(['append']))->true();

        expect($user->getJsonData('append'))->null();
        expect($user->getJsonData())->equals([$user::DATA_LNURL_OTT=>'tester-ott']);
    }





}
