<?php

namespace lnpay\events;

use lnpay\components\MailerComponent;
use Yii;

use yii\base\Component;
use yii\base\Event;

use lnpay\models\action\ActionFeed;
use lnpay\models\action\ActionName;
use lnpay\models\User;


class ActionEvent extends Event
{
    public $_customData = [];
    public $action_id;

    public $_userObject = NULL;


    public function __construct($d=[]) {
        $this->_customData = $d;
        parent::__construct();
    }

    public function getUserObject()
    {
        if ($this->_userObject)
            return $this->_userObject;
        else if (\LNPay::$app instanceof \yii\web\Application)
            return User::findOne(\LNPay::$app->user->id);
        else
            throw new \Exception('User id is missing!');

    }

    public function setUserObject($userObject)
    {
        $this->_userObject = $userObject;
    }

    public function getActionNameObject()
    {
        return ActionName::findOne($this->action_id);
    }

    public function getCustomData()
    {
        return $this->_customData;
    }



}