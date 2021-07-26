<?php

namespace app\controllers\v1;

use app\components\HelperComponent;
use app\models\v1\V1Layout;
use yii\filters\auth\QueryParamAuth;

use Yii;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

class LayoutController extends BaseApiController
{
    public $modelClass = 'app\models\v1\V1Layout';

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
            'create' => ['POST'],
            'update' => ['PUT','PATCH','POST'],
            //'delete' => ['DELETE'],
            'view' =>   ['GET'],
            //'index'=>   ['GET'],
        ];
    }

    /**
     *
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id,$gjs=false)
    {
        $modelClass = $this->modelClass;
        if ($layout = $modelClass::find()->where(['id'=>$id,'user_id'=>Yii::$app->user->id])->one()) {
            if ($gjs) {
                return json_decode($layout->html,TRUE);
            } else {
                return $layout;
            }

        } else {
            throw new NotFoundHttpException('Layout not found, or does not belong to this user');
        }
    }


    public function actionViewAll()
    {
        $modelClass = $this->modelClass;
        return new \yii\data\ActiveDataProvider([
            'query' => $modelClass::find()->where(['user_id'=>Yii::$app->user->id]),
            'pagination' => [
                'pageSize' => 100,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC
                ]
            ],
        ]);
    }


    public function actionUpdate($id)
    {
        if (!$model = V1Layout::find()->where(['id'=>$id,'user_id'=>Yii::$app->user->id])->one())
            throw new NotFoundHttpException('Layout not found, or does not belong to this user');
        $model->scenario = V1Layout::SCENARIO_API_EDITOR;

        $data = Yii::$app->getRequest()->getRawBody();
        $data = urldecode($data);
        parse_str($data,$result);

        //Array
        //(
        //    [gjs-assets] => []
        //    [gjs-css]

        if (isset($result['gjs-assets'])) {
            $model->html = json_encode($result);
        } else {
            $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        }


        $model->user_id = Yii::$app->user->id;

        if ($model->save()) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(200);

            return V1Layout::findOne($model->id);
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        } else {
            throw new BadRequestHttpException(HelperComponent::getErrorStringFromInvalidModel($model));
        }
    }

    public function actionCreate()
    {
        $model = new V1Layout(['scenario'=>V1Layout::SCENARIO_API_EDITOR]);

        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        $model->user_id = Yii::$app->user->id;

        if ($model->save()) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);

            return V1Layout::findOne($model->id);
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        } else {
            throw new BadRequestHttpException(HelperComponent::getErrorStringFromInvalidModel($model));
        }
    }



}
