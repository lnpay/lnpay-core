<?php

use yii\helpers\Url;

class HomeCest
{
    public function ensureThatLoginPage(AcceptanceTester $I)
    {
        $I->amOnPage(Url::toRoute('/home/login'));
        $I->see('Log in to ⚡LNPay');
    }

    public function ensureThatSignupPage(AcceptanceTester $I)
    {
        $I->amOnPage(Url::toRoute('/home/signup'));
        $I->see('Create your ⚡LNPay Account');
    }
}
