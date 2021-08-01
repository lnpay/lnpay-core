<?php
namespace lnpay\controllers;



use lnpay\models\action\ActionFeed;
use lnpay\models\wallet\WalletSearch;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use lnpay\models\User;
use lnpay\models\SignupForm;


use lnpay\components\AnalyticsComponent;

/**
 * Home controller
 */
class DashboardController extends BaseDashController
{
    const TIM_USER_ID = 147;

    public $user;

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

    /**
     * Displays settings.
     *
     * @return mixed
     */
    public function actionDevelopers()
    {
        return $this->render('developers');
    }

    /**
     * Displays events
     *
     * @return mixed
     */
    public function actionEvents()
    {
        $actionFeedQuery = ActionFeed::find()->where(['user_id'=>\LNPay::$app->user->id])->joinWith('actionName');

        $afDp = new \yii\data\ActiveDataProvider([
            'query' => $actionFeedQuery,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ]
            ],
        ]);

        return $this->render('events',['afDp'=>$afDp]);

    }

    /**
     * Displays settings.
     *
     * @return mixed
     */
    public function actionWebhooks()
    {
        return $this->render('webhooks');
    }


    public function actionHome()
    {
        return $this->render('home');
    }

}
