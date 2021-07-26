<?php

namespace app\modules\node\controllers;

use app\behaviors\UserAccessKeyBehavior;
use app\models\StatusType;
use app\modules\node\models\NodeAddForm;
use app\modules\node\models\NodeListener;
use Yii;
use app\modules\node\models\LnNode;
use app\models\LnNodeSearch;
use yii\data\ActiveDataProvider;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UnauthorizedHttpException;

/**
 * NodeController implements the CRUD actions for LnNode model.
 */
class LnController extends BaseNodeController
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

    public function beforeAction($event)
    {
        if (parent::beforeAction($event)) {

            //We can check for perms or other things on the current session key

            return true;
        }

    }

    /**
     * Displays a single LnNode model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionIndex()
    {
        $gi = $this->nodeObject->healthCheck('REST');
        $details = Yii::$app->session->getFlash('new_node_details',false);
        $node = $this->nodeObject;
        return $this->render('node', [
            'node' => $node,
            'details'=>$details,
            'gi'=>$gi
        ]);
    }

    /**
     * Displays a single LnNode model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionOnchain()
    {
        $details = Yii::$app->session->getFlash('new_node_details',false);
        $node = $this->nodeObject;

        $balances = $node->getLndConnector()->walletBalance();
        return $this->render('onchain', [
            'node' => $node,
            'balances'=>$balances
        ]);
    }

    public function actionNetworkfees()
    {
        return $this->render('network_fees',['node'=>$this->nodeObject]);
    }

    public function actionTestCall($call='getinfo')
    {
        switch ($call) {
            case 'getinfo':
                $gi = $this->nodeObject->healthCheck('REST');
                break;
        }
        if ($gi) {
            Yii::$app->session->setFlash('success','GetInfo successfully retrieved!');
        } else {
            Yii::$app->session->setFlash('error','GetInfo failed! Node is offline or unreachable');
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionConnect()
    {
        $node = $this->nodeObject;

        return $this->render('connect', [
            'node' => $node,
        ]);
    }

    /**
     * Updates an existing LnNode model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate()
    {
        $model = $this->nodeObject;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing LnNode model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $node = $this->nodeObject;
        $node->delete();

        return $this->redirect(['/node/dashboard/index']);
    }




}
