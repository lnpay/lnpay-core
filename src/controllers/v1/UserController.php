<?php

namespace lnpay\controllers\v1;

use lnpay\base\ApiController;
use lnpay\components\HelperComponent;
use lnpay\models\SignupForm;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;
use \lnpay\models\User;

class UserController extends ApiController
{
    public $modelClass = 'lnpay\models\User';

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
            'create'    => ['POST'],
            'update'    => ['PUT','PATCH','POST'],
            'delete'    => ['DELETE'],
            'view'      => ['GET'],
            'index'     => ['GET'],
            'send'      => ['POST']
        ];
    }

    /**
     * GET v1/user
     * @return \yii\web\IdentityInterface|null
     */
    public function actionView()
    {
        return User::findOne(\LNPay::$app->user->id);
    }

    /**
     * POST v1/user
     *
     * @return \lnpay\models\User|null
     * @throws BadRequestHttpException
     * @throws ServerErrorHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\UnauthorizedHttpException
     */
    public function actionCreate()
    {
        //Only admins can create
        $this->checkAdminAccess();

        $model = new SignupForm();
        $model->scenario = $model::SCENARIO_API_SIGNUP;

        $model->load(\LNPay::$app->getRequest()->getBodyParams(), '');
        if (!$model->email)
            $model->email = $model->username;
        $model->api_parent_id = \LNPay::$app->user->id;

        if ($model->validate() && $user = $model->signup()) {
            $response = \LNPay::$app->getResponse();
            $response->setStatusCode(201);

            return User::findOne($user->id);
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        } else {
            throw new BadRequestHttpException(HelperComponent::getFirstErrorFromFailedValidation($model));
        }
    }


}
