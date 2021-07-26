<?php

namespace app\components;

use app\models\log\UserApiLog;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\log\Target;
use Yii;

class ApiLogTarget extends Target {

    public function export() {
        if (stripos(Yii::$app->request->absoluteUrl,'/v1/')!==FALSE) {
            if (Yii::$app->user->identity) {

                $logMessage = new UserApiLog();
                $logMessage->created_at = time();
                $logMessage->api_key = Yii::$app->user->identity->sessionApiKey;
                $logMessage->external_hash = 'req_'.HelperComponent::generateRandomString(24);
                $logMessage->user_id = Yii::$app->user->identity->getId();
                $logMessage->method  = Yii::$app->request->getMethod();
                $logMessage->sdk = @Yii::$app->request->getHeaders()->toArray()['x-lnpay-sdk'][0];
                $logMessage->base_url = Yii::$app->request->hostInfo;
                $logMessage->ip_address = Yii::$app->request->userIP;
                $logMessage->request_path = Yii::$app->request->url;
                $logMessage->status_code = Yii::$app->response->statusCode;

                //Don't save GET request body
                if (Yii::$app->request->getMethod() != 'GET')
                    $logMessage->response_body = VarDumper::export(ArrayHelper::toArray(Yii::$app->response->data));

                $logMessage->response_headers = VarDumper::export(Yii::$app->response->getHeaders()->toArray());

                $logMessage->request_body = VarDumper::export(Yii::$app->request->rawBody);
                $logMessage->request_headers = VarDumper::export(Yii::$app->request->getHeaders()->toArray());
                $logMessage->save();
            }
        }
    }
}

?>