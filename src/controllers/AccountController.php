<?php

namespace lnpay\controllers;

use lnpay\models\ChangePasswordForm;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;


/**
 * WalletController implements the CRUD actions for Wallet model.
 */
class AccountController extends Controller
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(),[
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ]);
    }

    public function actionChangePassword()
    {
        $model = new ChangePasswordForm();
        $userModel = \lnpay\models\User::findOne(\LNPay::$app->user->id);

        if ($model->load(\LNPay::$app->request->post()) && $model->validate()) {
            try {
                $postData = \LNPay::$app->request->post();
                $userModel->setPassword($postData['ChangePasswordForm']['newPassword']);
                if ($userModel->save()) {
                    \LNPay::$app->getSession()->setFlash(
                        'success',
                        'Password changed'
                    );
                    return $this->redirect(['index']);
                } else {
                    \LNPay::$app->getSession()->setFlash(
                        'error',
                        'Password not changed'
                    );
                    return $this->redirect(['index']);
                }
            } catch (\Throwable $e) {
                \LNPay::$app->getSession()->setFlash(
                    'error',
                    (string)($e->getMessage())
                );
            }
        }

        return $this->render('_change-password', [
            'model'=>$model,
            'userModel' => $userModel
        ]);
    }
    public function actionIndex()
    {
        $userModel = \lnpay\models\User::findOne(\LNPay::$app->user->id);
        return $this->render('account-index', [
            'userModel' => $userModel
        ]);
    }

    public function actionMfa()
    {
        return $this->render('mfa');
    }

    public function actionProduct()
    {
        return $this->render('product');
    }
}
