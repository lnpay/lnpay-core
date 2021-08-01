<?php

namespace tests\unit\models;

use lnpay\components\ActionComponent;
use lnpay\models\action\ActionFeed;
use lnpay\models\action\ActionName;
use lnpay\models\User;

use lnpay\events\ActionEvent;
use lnpay\models\wallet\WalletTransaction;


class ActionComponentCest
{
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
