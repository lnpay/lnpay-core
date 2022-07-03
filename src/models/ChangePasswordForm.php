<?php

namespace lnpay\models;

use Yii;
use yii\base\Model;
use \lnpay\models\User;

/**
 * Class ChangePasswordForm
 * @package lnpay\models
 */
class ChangePasswordForm extends \yii\base\Model
{
    /**
     * @var
     */
    public $currentPassword;
    /**
     * @var
     */
    public $newPassword;
    /**
     * @var
     */
    public $confirmNewPassword;

    /**
     * Rules for change password  action
     * @return array
     */
    public function rules(): array
    {
        return [
            [['currentPassword', 'newPassword', 'confirmNewPassword'], 'required'],
            [['newPassword', 'confirmNewPassword'], 'string', 'min' => 6],
            ['currentPassword', 'findPasswords'],
            ['confirmNewPassword', 'compare', 'compareAttribute'=>'newPassword'],
        ];
    }

    /**
     * Verifying the current password with your existing password.
     * @param $attribute
     * @param $params
     * @throws \yii\base\Exception
     * @return void
     */
    public function findPasswords($attribute, $params): void
    {
        $user = User::findOne(\LNPay::$app->user->id);
        if (!$user->validatePassword($this->currentPassword)) {
            $this->addError($attribute, 'Current password is incorrect.');
        }
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'currentPassword'=>'Current Password',
            'newPassword'=>'New Password',
            'confirmNewPassword'=>'Confirm New Password',
        ];
    }
}
