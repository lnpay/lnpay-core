<?php
namespace app\models;

use Yii;
use yii\base\Model;
use app\models\User;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $api_parent_id;
    public $verifyCode;


    const SCENARIO_API_SIGNUP = 'api_signup';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            //['username', 'required'],
            ['username', 'unique', 'targetClass' => 'app\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => 'app\models\User', 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],

            ['api_parent_id', 'required'],
            ['api_parent_id', 'integer'],
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_API_SIGNUP => ['username', 'password', 'api_parent_id'],
            self::SCENARIO_DEFAULT => ['username', 'password', 'email','verifyCode'],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        
        $user = new User();
        $user->username = $this->email;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->api_parent_id = $this->api_parent_id;

        if ($this->scenario == self::SCENARIO_API_SIGNUP)
            $user->status = $user::STATUS_API_USER_LNTXBOT;
        
        return $user->save() ? $user : null;
    }
}
