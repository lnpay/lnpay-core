<?php

namespace lnpay\controllers;

use lnpay\base\DashController;
use lnpay\components\ActionComponent;
use lnpay\models\action\ActionName;
use lnpay\models\CustyDomain;
use yii\web\NotFoundHttpException;

/**
 * DomainController implements the CRUD actions for CustyDomain model.
 */
class DomainController extends DashController
{

    /**
     * Lists all IntegrationWebhook models.
     * @return mixed
     */
    public function actionIndex()
    {
        $domainQuery = CustyDomain::find()->where(['user_id'=>\LNPay::$app->user->id])->joinWith('statusType');

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $domainQuery,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single IntegrationWebhook model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $this->render('view',compact('model'));
    }

    /**
     * Creates a new IntegrationWebhook model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CustyDomain();
        $model->user_id = \LNPay::$app->user->id;

        if ($model->load(\LNPay::$app->request->post())) {
            if ($model->validate() && $model->save()) {
                \LNPay::$app->session->setFlash('success','Webhook created!');
                return $this->redirect(['index']);
            }

        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing IntegrationWebhook model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the IntegrationWebhook model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return IntegrationWebhook the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = IntegrationWebhook::find()->where(['external_hash'=>$id])->andWhere(['user_id'=>\LNPay::$app->user->id])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
