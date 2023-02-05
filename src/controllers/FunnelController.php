<?php
namespace lnpay\controllers;


use lnpay\base\DashController;
use lnpay\models\action\ActionFeed;
use lnpay\models\action\ActionName;
use lnpay\models\CustyDomain;
use lnpay\models\log\UserApiLog;
use lnpay\wallet\models\Wallet;
use lnpay\wallet\models\WalletTransaction;
use lnpay\wallet\models\WalletTransactionType;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;

use lnpay\models\User;

/**
 * Home controller
 */
class FunnelController extends DashController
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
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction'
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function beforeAction($event)
    {
        if (parent::beforeAction($event)) {
            if (\LNPay::$app->user->isGuest) {
                \LNPay::$app->session->setFlash('error','You must be logged in to access that!');
                return $this->redirect('/home/index')->send();
            }

            if (\LNPay::$app->user->id)
            {
                $this->user = User::findOne(\LNPay::$app->user->id);

                return TRUE;
            }
            else
                return FALSE;
        }
    }


    public function actionPlans()
    {
        return $this->render('plans');
    }
}
