<?php

namespace lnpay\controllers;

use lnpay\base\DashController;
use lnpay\models\log\UserApiLog;
use lnpay\models\log\UserApiLogSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ApiLogController implements the CRUD actions for UserApiLog model.
 */
class ApiLogController extends DashController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all UserApiLog models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserApiLogSearch();
        $searchModel->method = 'POST';
        $dataProvider = $searchModel->search(\LNPay::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserApiLog model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the UserApiLog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserApiLog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserApiLog::find()->where(['id'=>$id,'user_id'=>\LNPay::$app->user->id])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
