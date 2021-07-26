<?php

namespace app\controllers;

use app\components\HelperComponent;
use app\models\LnTx;
use app\models\User;
use app\models\wallet\LnWalletDepositForm;
use app\models\wallet\LnWalletWithdrawForm;
use app\models\wallet\WalletTransactionSearch;
use app\models\wallet\WalletTransferForm;
use Yii;
use app\models\wallet\Wallet;
use app\models\wallet\WalletSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\ServerErrorHttpException;

/**
 * WalletController implements the CRUD actions for Wallet model.
 */
class WalletController extends BaseDashController
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
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

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
        if (Yii::$app->request->isPost) {
            if ($wModel->load(Yii::$app->request->post())) {
                if (Yii::$app->session->getFlash('bt')) {
                    Yii::$app->session->setFlash('success', 'Withdrawal Sent!');
                    return $this->redirect(Yii::$app->request->referrer);
                }
            }
        }

        //Handle transfer
        if ($tModel->load(Yii::$app->request->post())) {
            try {
                $w = Wallet::findById($tModel->source_wallet_id);
                if (Yii::$app->user->id != $w->user_id) {
                    throw new \Exception('Invalid source wallet');
                }
            } catch (\Throwable $t) {
                Yii::$app->session->setFlash('error',$t->getMessage());
                return $this->redirect(Yii::$app->request->referrer);
            }

            if ($tModel->validate()) {
                $tModel->executeTransfer();
                Yii::$app->session->setFlash('success',"Transfer successful!");
                return $this->redirect(Yii::$app->request->referrer);
            } else {
                Yii::$app->session->setFlash('error',HelperComponent::getErrorStringFromInvalidModel($tModel));
                return $this->redirect(Yii::$app->request->referrer);
            }
        }




        $walletObject = $this->findModel($id);
        $walletObject->updateBalance();

        $availableWalletsForTransferQuery = Wallet::find()->where(['!=','external_hash',$id])->andWhere(['user_id'=>Yii::$app->user->id])->orderBy('balance DESC');

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

    public function actionKeysend($id)
    {
        $walletObject = $this->findModel($id);
        $searchModel = new WalletTransactionSearch();
        $searchModel->user_id = Yii::$app->user->id;
        $searchModel->wallet_id = $walletObject->id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $query = $dataProvider->query;
        $query->innerJoinWith('lnTx');
        $query->andFilterWhere(['ln_tx.is_keysend'=>1]);
        return $this->render('views/_keysend',['wallet'=>$walletObject,'wtxDataProvider'=>$dataProvider,'wtxSearchModel'=>$searchModel]);
    }

    public function actionTransactions($id)
    {
        $walletObject = $this->findModel($id);

        $searchModel = new WalletTransactionSearch();
        $searchModel->user_id = Yii::$app->user->id;
        $searchModel->wallet_id = $walletObject->id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('views/_wtx-breakdown',['wtxSearchModel'=>$searchModel,'wtxDataProvider'=>$dataProvider,'wallet'=>$walletObject]);
    }

    public function actionLnNode($id)
    {
        $walletObject = $this->findModel($id);
        return $this->render('views/_ln-node',['wallet'=>$walletObject]);
    }

    public function actionValidateWithdrawal($id)
    {
        $wModel = new LnWalletWithdrawForm();
        $wModel->wallet_id = $this->findModel($id)->id;

        if (Yii::$app->request->isAjax && $wModel->load(Yii::$app->request->post())) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            try {
                $bt = $wModel->processWithdrawal();
            } catch (\Throwable $e) { //man this is jank now
                return [Html::getInputId($wModel, 'payment_request')=>$wModel->getErrors('payment_request')];
            }

            if (empty($result)) {
                Yii::$app->session->setFlash('bt',true);
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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/wallet/view', 'id' => $model->id]);
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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
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
        if (($model = Wallet::find()->where(['user_id'=>Yii::$app->user->id,'id'=>$id])->orWhere(['external_hash'=>$id,'user_id'=>Yii::$app->user->id])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
