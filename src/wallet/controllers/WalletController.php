<?php

namespace lnpay\wallet\controllers;

use lnpay\base\DashController;
use lnpay\components\HelperComponent;

use lnpay\wallet\models\LnLoopOutForm;
use lnpay\wallet\models\LnWalletDepositForm;
use lnpay\wallet\models\LnWalletWithdrawForm;
use lnpay\wallet\models\WalletNodeChangeForm;
use lnpay\wallet\models\WalletTransactionSearch;
use lnpay\wallet\models\WalletTransferForm;

use lnpay\wallet\models\Wallet;
use lnpay\wallet\models\WalletSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * WalletController implements the CRUD actions for Wallet model.
 */
class WalletController extends DashController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(),[
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ]);
    }

    /**
     * Lists all Wallet models.
     * @return mixed
     */
    public function actionDashboard()
    {
        $searchModel = new WalletSearch();
        $searchModel->user_id = $this->user->id;
        $dataProvider = $searchModel->search(\LNPay::$app->request->queryParams);

        return $this->render('dashboard',compact('searchModel','dataProvider'));
    }

    /**
     * Displays a single Wallet model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $wModel = new LnWalletWithdrawForm();
        $dModel = new LnWalletDepositForm();
        $tModel = new WalletTransferForm();

        //Handle Withdrawal
        if (\LNPay::$app->request->isPost) {
            if ($wModel->load(\LNPay::$app->request->post())) {
                if (\LNPay::$app->session->getFlash('bt')) {
                    \LNPay::$app->session->setFlash('success', 'Withdrawal Sent!');
                    return $this->redirect(\LNPay::$app->request->referrer);
                }
            }
        }

        //Handle transfer
        if ($tModel->load(\LNPay::$app->request->post())) {
            try {
                $w = Wallet::findById($tModel->source_wallet_id);
                if (\LNPay::$app->user->id != $w->user_id) {
                    throw new \Exception('Invalid source wallet');
                }
            } catch (\Throwable $t) {
                \LNPay::$app->session->setFlash('error',$t->getMessage());
                return $this->redirect(\LNPay::$app->request->referrer);
            }

            if ($tModel->validate()) {
                $tModel->executeTransfer();
                \LNPay::$app->session->setFlash('success',"Transfer successful!");
                return $this->redirect(\LNPay::$app->request->referrer);
            } else {
                \LNPay::$app->session->setFlash('error',HelperComponent::getFirstErrorFromFailedValidation($tModel));
                return $this->redirect(\LNPay::$app->request->referrer);
            }
        }




        $walletObject = $this->findModel($id);
        //$walletObject->updateBalance();

        $availableWalletsForTransferQuery = Wallet::find()->where(['!=','external_hash',$id])->andWhere(['user_id'=>\LNPay::$app->user->id])->orderBy('balance DESC');

        return $this->render('view', [
            'wallet' => $walletObject,
            'wModel' => $wModel,
            'dModel' => $dModel,
            'tModel' => $tModel,
            'availableWalletsForTransferQuery' => $availableWalletsForTransferQuery,
        ]);
    }

    public function actionAccessKeys($id)
    {
        $walletObject = $this->findModel($id);
        return $this->render('views/_access-keys',['wallet'=>$walletObject]);
    }

    public function actionLnurlPay($id)
    {
        $walletObject = $this->findModel($id);
        return $this->render('views/_lnurl-pay',['wallet'=>$walletObject]);
    }

    public function actionLoop($id)
    {
        $walletObject = $this->findModel($id);
        $lnLoopOutForm = new LnLoopOutForm();

        $lnLoopOutForm->wallet_id = $walletObject->external_hash;
        if ($lnLoopOutForm->load(\LNPay::$app->request->post()) && $lnLoopOutForm->validate()) {
            $lnLoopOutForm->attemptLoopOut();
            return $this->redirect(\LNPay::$app->request->referrer);
        }
        return $this->render('views/_loop',['wallet'=>$walletObject,'lnLoopOutForm'=>$lnLoopOutForm]);
    }

    public function actionKeysend($id)
    {
        $walletObject = $this->findModel($id);
        $searchModel = new WalletTransactionSearch();
        $searchModel->user_id = \LNPay::$app->user->id;
        $searchModel->wallet_id = $walletObject->id;
        $dataProvider = $searchModel->search(\LNPay::$app->request->queryParams);
        $query = $dataProvider->query;
        $query->innerJoinWith('lnTx');
        $query->andFilterWhere(['ln_tx.is_keysend'=>1]);
        return $this->render('views/_keysend',['wallet'=>$walletObject,'wtxDataProvider'=>$dataProvider,'wtxSearchModel'=>$searchModel]);
    }

    public function actionTransactions($id)
    {
        $walletObject = $this->findModel($id);

        $searchModel = new WalletTransactionSearch();
        $searchModel->user_id = \LNPay::$app->user->id;
        $searchModel->wallet_id = $walletObject->id;
        $dataProvider = $searchModel->search(\LNPay::$app->request->queryParams);
        return $this->render('views/_wtx-breakdown',['wtxSearchModel'=>$searchModel,'wtxDataProvider'=>$dataProvider,'wallet'=>$walletObject]);
    }

    public function actionLnNode($id)
    {
        $walletNodeChangeForm = new WalletNodeChangeForm();
        if ($walletNodeChangeForm->load(\LNPay::$app->request->post())) {
            if ($walletNodeChangeForm->validate()) {
                $result = $walletNodeChangeForm->switchWalletTargetNode();
                if ($result) {
                    return $this->redirect(\LNPay::$app->request->referrer);
                }
            }
        }

        $walletObject = $this->findModel($id);
        return $this->render('views/_ln-node',['wallet'=>$walletObject,'walletNodeChangeForm'=>$walletNodeChangeForm]);
    }

    public function actionValidateWithdrawal($id)
    {
        $wModel = new LnWalletWithdrawForm();
        $wModel->wallet_id = $this->findModel($id)->id;

        if (\LNPay::$app->request->isAjax && $wModel->load(\LNPay::$app->request->post())) {
            \LNPay::$app->response->format = \yii\web\Response::FORMAT_JSON;

            try {
                $bt = $wModel->processWithdrawal();
            } catch (\Throwable $e) { //man this is jank now
                return [Html::getInputId($wModel, 'payment_request')=>$wModel->getErrors('payment_request')];
            }

            if (empty($result)) {
                \LNPay::$app->session->setFlash('bt',true);
                return [];
            } else {
                return $result;
            }
        }
    }

    /**
     * Creates a new Wallet model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Wallet();

        if ($model->load(\LNPay::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Wallet model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
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
     * Deletes an existing Wallet model.
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
     * Finds the Wallet model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Wallet the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Wallet::find()->where(['user_id'=>\LNPay::$app->user->id,'id'=>$id])->orWhere(['external_hash'=>$id,'user_id'=>\LNPay::$app->user->id])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
