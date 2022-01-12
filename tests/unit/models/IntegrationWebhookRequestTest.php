<?php

namespace tests\unit\models;

use lnpay\models\action\ActionFeed;
use lnpay\models\action\ActionName;
use lnpay\models\integration\IntegrationWebhook;
use lnpay\models\integration\IntegrationWebhookRequest;
use lnpay\models\LnTx;
use lnpay\models\StatusType;
use lnpay\models\User;
use lnpay\wallet\models\Wallet;
use lnpay\fixtures\UserFixture;
use lnpay\fixtures\WalletFixture;
use Yii;

class IntegrationWebhookRequestTest extends \Codeception\Test\Unit
{
    public $tester;

    public function _fixtures()
    {
        return [
            'users' => [
                'class' => \lnpay\fixtures\UserFixture::class,
            ],
            'wallets' => [
                'class' => \lnpay\fixtures\WalletFixture::class,
            ],
            'lntx' => [
                'class' => \lnpay\fixtures\LnTxFixture::class,
            ],
            'user_access_key' => [
                'class' => \lnpay\fixtures\UserAccessKeyFixture::class,
            ],
            'wallet_lnurlpay' => [
                'class' => \lnpay\fixtures\WalletLnurlpayFixture::class,
            ]
        ];
    }

    protected function _before()
    {
        expect_that($i = new IntegrationWebhook());
        expect_that($i->user_id = 147);
        expect_that($i->endpoint_url = 'http://127.0.0.1');
        expect_that($i->action_name_id = ['default_all']);
        expect($i->save())->true();
    }

    public function testWebhookRequest()
    {
        expect_that($u = User::findOne(147));
        expect_that($wallet = Wallet::findOne(6));

        expect_that($actionFeedObject = $u->registerAction(ActionName::WALLET_CREATED,['wal'=>$wallet->toArray()]));
        expect(IntegrationWebhookRequest::find()->where(['action_feed_id'=>$actionFeedObject->id])->exists())->true();
    }

    public function testPreparePayload()
    {
        expect_that($id = User::findOne(147)->registerAction(ActionName::USER_CREATED,['wallet'=>123]));
        expect_that($testActionFeed = ActionFeed::findOne($id));

        expect(IntegrationWebhookRequest::preparePayload($testActionFeed,['id'=>'id_1234']))->hasKey('id');
    }

    public function testPrepareRequest()
    {
        expect_that($id = User::findOne(147)->registerAction(ActionName::USER_CREATED,['wallet'=>123]));
        expect_that($testActionFeed = ActionFeed::findOne($id));

        expect(IntegrationWebhookRequest::preparePayload($testActionFeed,['id'=>'id_1234']))->hasKey('id');

        expect_that($IW = IntegrationWebhook::findOne(5));
        expect(IntegrationWebhookRequest::prepareRequest($IW,$testActionFeed))->isInstanceOf(IntegrationWebhookRequest::class);
    }

    public function testProcessResponse()
    {
        //@TODO: simulate guzzle response
    }
}
