<?php

namespace tests\unit\models;

use lnpay\models\LoginForm;

class LoginFormTest extends \Codeception\Test\Unit
{
    private $model;

    protected function _after()
    {
        \LNPay::$app->user->logout();
    }

    public function testLoginNoUser()
    {
        $this->model = new LoginForm([
            'username' => 'not_existing_username',
            'password' => 'not_existing_password',
        ]);

        expect_not($this->model->login());
        expect_that(\LNPay::$app->user->isGuest);
    }

    public function testLoginWrongPassword()
    {
        $this->model = new LoginForm([
            'username' => 'demo',
            'password' => 'wrong_password',
        ]);

        expect_not($this->model->login());
        expect_that(\LNPay::$app->user->isGuest);
        expect($this->model->errors)->hasKey('password');
    }

}
