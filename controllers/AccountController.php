<?php

namespace app\controllers;

use app\models\ChangePasswordForm;
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

    public function actionIndex()
    {
        $model = new ChangePasswordForm();
        $userModel = \app\models\User::findOne(Yii::$app->user->id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            try {
                $postData = Yii::$app->request->post();
                $userModel->setPassword($postData['ChangePasswordForm']['newPassword']);
                if ($userModel->save()) {
                    Yii::$app->getSession()->setFlash(
                        'success',
                        'Password changed'
                    );
                    return $this->redirect(['index']);
                } else {
                    Yii::$app->getSession()->setFlash(
                        'error',
                        'Password not changed'
                    );
                    return $this->redirect(['index']);
                }
            } catch (Exception $e) {
                Yii::$app->getSession()->setFlash(
                    'error',
                    (string)($e->getMessage())
                );
                return $this->render('account-index', [
                    'model' => $model,
                    'userModel' => $userModel
                ]);
            }
        }

        return $this->render('account-index', [
            'model' => $model,
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
