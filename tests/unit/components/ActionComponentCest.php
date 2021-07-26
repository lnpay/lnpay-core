<?php

namespace tests\unit\models;

use app\components\ActionComponent;
use app\models\action\ActionFeed;
use app\models\action\ActionName;
use app\models\User;

use app\events\ActionEvent;
use app\models\wallet\WalletTransaction;


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
