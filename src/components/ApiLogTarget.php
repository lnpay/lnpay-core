<?php

namespace lnpay\components;

use lnpay\models\log\UserApiLog;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\log\Target;
use Yii;

class ApiLogTarget extends Target {

    public function export() {
        if (stripos(\LNPay::$app->request->absoluteUrl,'/v1/')!==FALSE) {
            if (\LNPay::$app->user->identity) {

                $logMessage = new UserApiLog();
                $logMessage->created_at = time();
                $logMessage->api_key = \LNPay::$app->user->identity->sessionApiKey;
                $logMessage->external_hash = 'req_'.HelperComponent::generateRandomString(24);
                $logMessage->user_id = \LNPay::$app->user->identity->getId();
                $logMessage->method  = \LNPay::$app->request->getMethod();
                $logMessage->sdk = @\LNPay::$app->request->getHeaders()->toArray()['x-lnpay-sdk'][0];
                $logMessage->base_url = \LNPay::$app->request->hostInfo;
                $logMessage->ip_address = \LNPay::$app->request->userIP;
                $logMessage->request_path = \LNPay::$app->request->url;
                $logMessage->status_code = \LNPay::$app->response->statusCode;

                //Don't save GET request body
                if (\LNPay::$app->request->getMethod() != 'GET')
                    $logMessage->response_body = VarDumper::export(ArrayHelper::toArray(\LNPay::$app->response->data));

                $logMessage->response_headers = VarDumper::export(\LNPay::$app->response->getHeaders()->toArray());

                $logMessage->request_body = VarDumper::export(\LNPay::$app->request->rawBody);
                $logMessage->request_headers = VarDumper::export(\LNPay::$app->request->getHeaders()->toArray());
                $logMessage->save();

                AnalyticsComponent::log(Yii::$app->user->id,'api_request',$logMessage->amplitudeAttributeValues);
            }
        }
    }
}

?>