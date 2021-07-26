<?php

namespace tests\unit\models;

use app\behaviors\UserAccessKeyBehavior;
use app\modules\node\models\LnNode;
use app\modules\node\models\NodeListener;
use app\models\wallet\Wallet;
use app\models\StatusType;
use app\models\User;
use Yii;

class LnNodeTest extends \Codeception\Test\Unit
{
    public $tester;
    public $basePath;

    protected function _before()
    {
        \Yii::$app->user->login(User::findIdentity(147));
        $this->basePath = getenv('SUPERVISOR_CONF_PATH');
        $this->node = LnNode::findOne('lnod_test1');
    }

    protected function _after()
    {

    }

    public function testGetIsRpcUp()
    {
        expect($this->node->isRpcUp)->true();
    }

    public function testGetIsRestUp()
    {
        expect($this->node->isRestUp)->true();
    }

    public function testSetRestErrorState()
    {
        expect($this->node->setRestErrorState(['message'=>'down']))->true();
        expect($this->node->getJsonData('rest_error_message'))->equals('down');

        expect($this->node->setRestErrorState(['message'=>'down']))->null();
    }

    public function testSetRestUpState()
    {
        expect($this->node->setRestUpState())->true();
        expect($this->node->getJsonData('rest_error_message'))->null();
    }

    public function testSetRpcErrorState()
    {
        expect($this->node->setRpcErrorState(['message'=>'down1']))->true();
        expect($this->node->getJsonData('rpc_error_message'))->equals('down1');

        expect($this->node->setRpcErrorState(['message'=>'down']))->null();
    }

    public function testSetRpcUpState()
    {
        expect($this->node->setRpcUpState())->true();
        expect($this->node->getJsonData('rpc_error_message'))->null();
    }

    public function testHealthCheck()
    {
        //No need to test this, calls above
    }



}
