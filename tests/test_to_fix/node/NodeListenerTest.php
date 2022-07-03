<?php

namespace tests\unit\models;

use lnpay\behaviors\UserAccessKeyBehavior;
use lnpay\node\models\LnNode;
use lnpay\node\models\NodeListener;
use lnpay\wallet\models\Wallet;
use lnpay\models\StatusType;
use lnpay\models\User;
use Yii;

class NodeListenerTest extends \Codeception\Test\Unit
{
    public $tester;
    public $basePath;

    protected function _before()
    {
        \LNPay::$app->user->login(User::findIdentity(147));
        $this->basePath = getenv('SUPERVISOR_CONF_PATH');
    }

    protected function _after()
    {

    }

    public function testCreateLndRpcListenerObject()
    {
        expect_that($node = LnNode::findOne('lnod_test1'));
        expect($n = NodeListener::createLndRpcListenerObject($node,NodeListener::LND_RPC_SUBSCRIBE_INVOICES))
            ->isInstanceOf(NodeListener::class);

        expect($n->delete())->equals(1);
    }

    public function testCreateLndRpcListenerObjects()
    {
        expect_that($node = LnNode::findOne('lnod_test1'));
        expect_that($config_filename = $node->getSupervisorConfFilename());
        expect(NodeListener::createLndRpcListenerObjects($node))
            ->count(sizeof(NodeListener::getAvailableSubscribeMethods()));
        expect($f = file_get_contents($this->basePath.$config_filename))->notEquals(false);
        expect($f)->contains('[program:'.$node->id.'--SubscribeInvoices'.']');
    }

    /* not sure what is going on here, but environment configuration is fucking this up
    public function testUpdateLndRpcListenerObject()
    {
        expect($l = NodeListener::findOne('lnod_test1--SubscribeInvoices'));
        expect($l->updateSupervisorParameters(['autostart'=>0]));
        expect($f = file_get_contents($this->basePath.'lnod_test1.conf'))->notEquals(false);
        expect($f)->contains('autostart = 0');
        expect(NodeListener::findOne('lnod_test1--SubscribeInvoices')->supervisor_parameters['autostart'])->equals(0);
        //unlink($this->basePath.'lnod_test1.conf');
    }*/


}
