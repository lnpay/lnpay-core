<?php

namespace lnpay\node\controllers;

use lnpay\behaviors\UserAccessKeyBehavior;
use lnpay\models\StatusType;
use lnpay\node\models\LnNodeProfile;
use lnpay\node\models\NodeAddForm;
use lnpay\node\models\NodeListener;
use lnpay\wallet\models\Wallet;
use Yii;
use lnpay\node\models\LnNode;
use lnpay\models\LnNodeSearch;
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
        $details = \LNPay::$app->session->getFlash('new_node_details',false);
        $node = $this->nodeObject;

        return $this->render('node', [
            'node' => $node,
            'details'=>$details,
            'gi'=>$gi
        ]);
    }

    public function actionSettings()
    {
        $node = $this->nodeObject;
        return $this->render('settings', [
            'node' => $node,
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
        $details = \LNPay::$app->session->getFlash('new_node_details',false);
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
            \LNPay::$app->session->setFlash('success','GetInfo successfully retrieved!');
        } else {
            \LNPay::$app->session->setFlash('error','GetInfo failed! Node is offline or unreachable');
        }
        return $this->redirect(\LNPay::$app->request->referrer);
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

        if ($model->load(\LNPay::$app->request->post()) && $model->save()) {
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
        $node = LnNode::findOne(['id'=>$id,'user_id'=>Yii::$app->user->id]);

        $node->delete();

        Yii::$app->session->setFlash('success','Node Removed');
        return $this->redirect(['/dashboard/home']);
    }




}
