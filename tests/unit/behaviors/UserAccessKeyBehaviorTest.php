<?php

namespace tests\unit\models;

use app\models\User;
use app\models\UserAccessKey;

class UserAccessKeyBehaviorTest extends \Codeception\Test\Unit
{

    public function testGetUserAccessKeys()
    {
        $user = new User();
        $user->username = 'tester-access-key';
        $user->email = 'tester-access-key@gmail.com';
        $user->auth_key = '111';
        $user->password_hash = '11';
        $user->password = 'aa';
        $user->save();

        expect($user->getUserAccessKeys())->hasKey('Public API Key');
        expect($user->getUserAccessKeys())->hasKey('Secret API Key');

    }

}