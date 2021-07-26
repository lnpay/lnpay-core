<?php

namespace app\controllers;

use app\components\ActionComponent;
use app\models\action\ActionName;
use app\models\integration\IntegrationWebhookRequest;
use app\models\integration\WebhookTestForm;
use Yii;
use app\models\integration\IntegrationWebhook;
use app\models\integration\IntegrationWebhookSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * WebhookController implements the CRUD actions for IntegrationWebhook model.
 */
class WebhookController extends BaseDashController
{

    /**
     * Lists all IntegrationWebhook models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new IntegrationWebhookSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
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

    public function actionRedeliver($iwhr_id)
    {
        $iwhr = IntegrationWebhookRequest::find()->where(['external_hash'=>$iwhr_id])->one();
        $IW = $iwhr->integrationWebhook;

        $iwhr = IntegrationWebhookRequest::prepareRequest($IW,$iwhr->actionFeed);
        ActionComponent::webhookRequest($iwhr);

        Yii::$app->session->setFlash('success','Webhook sent');
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionTestWebhook()
    {
        $model = new WebhookTestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $IW = IntegrationWebhook::find()->where(['external_hash'=>$model->integration_webhook_id])->one();
            $actionFeedObject = ActionComponent::getTestWebhookActionFeedObject(ActionName::find()->where(['name'=>$model->action_id])->one()->id);
            $iwhr = IntegrationWebhookRequest::prepareRequest($IW,$actionFeedObject);
            $iwhr = ActionComponent::webhookRequest($iwhr);

            $iwhr->delete();

            Yii::$app->session->setFlash('success','Test Webhook sent');
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Creates a new IntegrationWebhook model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new IntegrationWebhook();
        $model->user_id = Yii::$app->user->id;

        if ($model->load(Yii::$app->request->post())) {
            $names = [];
            foreach ($model->action_name_id as $type => $action_ids) {
                if (is_array($action_ids))
                    foreach ($action_ids as $a)
                        $names[] = $a;
            }
            $model->action_name_id = $names;

            if ($model->save()) {
                Yii::$app->session->setFlash('success','Webhook created!');
                return $this->redirect(['index', 'id' => $model->external_hash]);
            }

        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing IntegrationWebhook model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $names = [];
            foreach ($model->action_name_id as $type => $action_ids) {
                if (is_array($action_ids))
                    foreach ($action_ids as $a)
                        $names[] = $a;
            }
            $model->action_name_id = $names;

            if ($model->save()) {
                Yii::$app->session->setFlash('success','Webhook updated!');
                return $this->redirect(['index', 'id' => $model->external_hash]);
            }


        }

        return $this->render('update', [
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
        if (($model = IntegrationWebhook::find()->where(['external_hash'=>$id])->andWhere(['user_id'=>Yii::$app->user->id])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
