<?php

namespace tests\unit\models;

use lnpay\components\HelperComponent;
use lnpay\models\ChangePasswordForm;
use lnpay\models\User;
use lnpay\fixtures\UserAccessKeyFixture;
use lnpay\fixtures\UserFixture;
use \Codeception\Test\Unit;

/**
 * Class ChangePasswordFormTest
 * @package tests\unit\models
 */
class ChangePasswordFormTest extends Unit
{
    /**
     * @var
     */
    private $model;
    public function _fixtures()
    {
        return [
            'users' => [
                'class' => UserFixture::class,
            ]
        ];
    }

    /**
     * Preset user session
     */
    protected function _before()
    {
        \LNPay::$app->user->login(User::findIdentity(147));
    }


    /**
     * @return void
     */
    public function testChangePassword(): void
    {
        $model = new ChangePasswordForm();
        $newPassword = 'abcdef';
        expect_that($model->newPassword = $newPassword);
        expect_that($model->confirmNewPassword = $newPassword);

        // Current password is incorrect.
        expect_that($model->currentPassword = '123451');
        expect($model->validate())
            ->false();
        codecept_debug(HelperComponent::getFirstErrorFromFailedValidation($model));
        expect(HelperComponent::getFirstErrorFromFailedValidation($model))
            ->equals("Current password is incorrect.");

        // Current password is correct.
        expect_that($model->currentPassword = '123456');
        expect($model->validate())
            ->true();
    }
}