<?php

namespace tests\unit\models;

use lnpay\models\BaseLink;
use lnpay\models\integration\IntegrationWebhook;
use lnpay\models\LnTx;
use lnpay\models\StatusType;
use lnpay\models\User;
use lnpay\fixtures\UserAccessKeyFixture;
use lnpay\fixtures\UserFixture;
use Yii;

class IntegrationWebhookTest extends \Codeception\Test\Unit
{
    public $tester;

    public function _fixtures()
    {
        return [
            'users' => [
                'class' => UserFixture::class,
            ]
        ];
    }

    protected function _before()
    {

    }

    public function testAddWebhook()
    {
        expect_that($i = new IntegrationWebhook());
        expect_that($i->user_id = 147);
        expect_that($i->endpoint_url = 'http://127.0.0.1');
        expect_that($i->action_name_id = ['default_all']);
        expect($i->save())->true();
    }


    public function testAddWebhookFailInvalidAction()
    {
        expect_that($i = new IntegrationWebhook());
        expect_that($i->user_id = 147);
        expect_that($i->endpoint_url = 'http://127.0.0.1');
        expect_that($i->action_name_id = ['wrong_action']);
        expect($i->save())->false();

        $errorArray = ['action_name_id'=>['Invalid event: wrong_action']];
        expect($i->getErrors())->equals($errorArray);
    }

    public function testAddWebhookFailNoArray()
    {
        expect_that($i = new IntegrationWebhook());
        expect_that($i->user_id = 147);
        expect_that($i->endpoint_url = 'http://127.0.0.1');
        expect_that($i->action_name_id = 'www');
        expect($i->save())->false();

        $errorArray = ['action_name_id'=>['Must supply array of event names']];
        expect($i->getErrors())->equals($errorArray);
    }
}
