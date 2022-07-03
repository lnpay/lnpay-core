<?php

namespace lnpay\controllers\v1;

use lnpay\base\ApiController;
use lnpay\components\HelperComponent;
use lnpay\models\action\ActionFeed;
use lnpay\models\action\ActionName;
use lnpay\models\integration\IntegrationService;
use lnpay\models\integration\IntegrationWebhook;
use lnpay\wallet\models\Wallet;

use Yii;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;

class WebhookController extends ApiController
{
    public $modelClass = 'lnpay\models\integration\IntegrationWebhook';

    public function actions(){
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['view']);

        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['index']);
        return $actions;
    }

    protected function verbs(){
        return [
            //'create' => ['POST'],
            //'update' => ['PUT','PATCH','POST'],
            //'delete' => ['DELETE'],
            'subscribe' =>   ['POST'],
            'unsubscribe'=>   ['DELETE'],
        ];
    }

    public function actionSubscribe($serviceId)
    {
        $body = \LNPay::$app->getRequest()->getBodyParams();
        $params = \LNPay::$app->getRequest()->getQueryParams();
        \LNPay::info("Body: ".print_r($body,TRUE),__METHOD__);
        \LNPay::info("Request URL: ".print_r($params,TRUE),__METHOD__);

        if (!@$body['hookUrl'] || !@$body['action_name_id'])
            throw new BadRequestHttpException('Webhook URL not present / action not present');

        $actionObject = ActionName::find()->where(['name'=>$body['action_name_id']])->one();
        if (!$actionObject)
            throw new BadRequestHttpException('Invalid action ID');

        $serviceObject = IntegrationService::find()->where(['name'=>$serviceId])->one();
        if (!$serviceObject)
            throw new BadRequestHttpException('Invalid Service ID!');

        $hookUrl = $body['hookUrl'];

        $wallet = Wallet::find()->where(['external_hash'=>@$body['wallet_id']])->one();
        if ($wallet && ($wallet->user_id != \LNPay::$app->user->id)) {
            throw new BadRequestHttpException('You do not have link permission!');
        }

        $model = new IntegrationWebhook();
        $model->wallet_id = @$wallet->id;
        $model->user_id = \LNPay::$app->user->id;
        $model->action_name_id = [$actionObject->name];
        $model->http_method = 'POST';
        $model->endpoint_url = $hookUrl;
        $model->integration_service_id = IntegrationService::SERVICE_ZAPIER;

        if ($model->save()) {
            $response = \LNPay::$app->getResponse();
            $response->setStatusCode(201);

            $af = ActionFeed::find()->where(['user_id'=>\LNPay::$app->user->id,'action_name_id'=>$actionObject->id])->orderBy('id DESC')->one();

            if ($model->endpoint_url == 'https://hooks.zapier.com/fake-subscription-url')
                $model->delete();

            return (@$af->actionData?:true);
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        } else {
            throw new BadRequestHttpException(HelperComponent::getFirstErrorFromFailedValidation($model));
        }
    }


    public function actionUnsubscribe($serviceId)
    {
        $params = \LNPay::$app->getRequest()->getQueryParams();
        \LNPay::info("Request URL: ".print_r($params,TRUE),__METHOD__);

        $serviceObject = IntegrationService::find()->where(['name'=>$serviceId])->one();
        if (!$serviceObject)
            throw new BadRequestHttpException('Invalid Service ID!');

        if (!@$params['hookUrl'])
            throw new BadRequestHttpException('Webhook URL not present');


        $hookUrl = $params['hookUrl'];

        $defaultActionWhere = []; //['IS NOT',new \yii\db\JsonExpression("JSON_EXTRACT(`action_name_id`, '$.{$actionObject->name}');"),NULL];
        $model = IntegrationWebhook::find()->where(['endpoint_url'=>$hookUrl,'user_id'=>\LNPay::$app->user->id])->andWhere($defaultActionWhere)->one();

        if ($model->delete()) {
            $response = \LNPay::$app->getResponse();
            $response->setStatusCode(200);

            return true;
        } else {
            \LNPay::error($model->id,__METHOD__);
            throw new BadRequestHttpException('Failed to delete object for unknown reason');
        }
    }
}
