<?php

namespace tests\unit\models;

use lnpay\components\ActionComponent;
use lnpay\models\action\ActionFeed;
use lnpay\models\action\ActionName;
use lnpay\models\User;

use lnpay\events\ActionEvent;
use lnpay\wallet\models\Wallet;
use lnpay\wallet\models\WalletTransaction;


class ActionComponentCest
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
            'user_access_key' => [
                'class' => \lnpay\fixtures\UserAccessKeyFixture::class,
            ]
        ];
    }

    public function test_user_created(\FunctionalTester $I)
    {
        $user = new User();
        $user->username = 'a777a@aa.com';
        $user->email = 'a777a@aa.com';
        $user->setPassword('a777a@aa.com');
        $user->generateAuthKey();
        $user->save();
        expect_that($event = new ActionEvent(['user'=>$user]));
        expect_that($event->action_id = ActionName::USER_CREATED);
        expect_that($event->userObject = $user);
        ActionComponent::user_created($event);
    }

}
