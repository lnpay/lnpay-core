<?php

namespace app\controllers\v1;

use app\components\HelperComponent;
use app\models\SignupForm;
use Codeception\Lib\Generator\Helper;
use yii\filters\auth\QueryParamAuth;

use Yii;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use \app\models\User;

use \tkijewski\lnurl;

class UserController extends BaseApiController
{
    public $modelClass = 'app\models\User';

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
        return User::findOne(Yii::$app->user->id);
    }

    /**
     * POST v1/user
     *
     * @return \app\models\User|null
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

        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if (!$model->email)
            $model->email = $model->username;
        $model->api_parent_id = Yii::$app->user->id;

        if ($model->validate() && $user = $model->signup()) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);

            return User::findOne($user->id);
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        } else {
            throw new BadRequestHttpException(HelperComponent::getErrorStringFromInvalidModel($model));
        }
    }


}
