<?php

namespace lnpay\node\controllers;

use lnpay\node\models\LnNode;
use Yii;
use lnpay\node\models\LnNodeProfile;
use lnpay\node\models\LnNodeProfileSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LnNodeProfileController implements the CRUD actions for LnNodeProfile model.
 */
class AuthprofileController extends BaseNodeController
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
     * Lists all LnNodeProfile models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LnNodeProfileSearch();
        $searchModel->ln_node_id = $this->nodeObject->id;
        $searchModel->user_id = \LNPay::$app->user->id;
        $dataProvider = $searchModel->search(\LNPay::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single LnNodeProfile model.
     * @param resource $id
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
     * Creates a new LnNodeProfile model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new LnNodeProfile();
        $model->ln_node_id = $this->nodeObject->id;
        if ($model->load(\LNPay::$app->request->post())) {
            if ($prof = $model->bakeMacaroon()) {
                return $this->redirect(['view', 'id' => $prof->id]);
            }
        }


        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing LnNodeProfile model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param resource $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(\LNPay::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing LnNodeProfile model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param resource $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        \LNPay::$app->session->setFlash('error','Cannot delete macaroons yet!');
        return $this->redirect(\LNPay::$app->request->referrer);
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the LnNodeProfile model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param resource $id
     * @return LnNodeProfile the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LnNodeProfile::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
