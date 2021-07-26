<?php

namespace tests\unit\models;

use app\components\HelperComponent;
use app\models\ChangePasswordForm;
use app\models\User;
use app\tests\fixtures\UserAccessKeyFixture;
use app\tests\fixtures\UserFixture;
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
        \Yii::$app->user->login(User::findIdentity(147));
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
        codecept_debug(HelperComponent::getErrorStringFromInvalidModel($model));
        expect(HelperComponent::getErrorStringFromInvalidModel($model))
            ->equals("Current password is incorrect.");

        // Current password is correct.
        expect_that($model->currentPassword = '123456');
        expect($model->validate())
            ->true();
    }
}