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
class DashboardController extends DashController
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
        $user_id = \LNPay::$app->user->id;
        $actionFeedQuery = ActionFeed::find()
            ->where(['user_id'=>$user_id])
            ->joinWith('actionName');

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

        $actionFeedQuery = ActionFeed::find()
            ->where(['user_id'=>$user_id])
            ->joinWith('actionName')
            ->andWhere(['action_name_id'=>ActionName::WALLET_SEND_FAILURE]);

        $afDpFailed = new \yii\data\ActiveDataProvider([
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

        $actionFeedQuery = WalletTransaction::find()
            ->where(['user_id'=>$user_id])
            ->andWhere(['wtx_type_id'=>[
                WalletTransactionType::LN_WITHDRAWAL,
                WalletTransactionType::LN_DEPOSIT,
                WalletTransactionType::LN_LNURL_PAY_INBOUND,
                WalletTransactionType::LN_LNURL_PAY_OUTBOUND
            ]])
            ->limit(10);

        $afDpSuccess = new \yii\data\ActiveDataProvider([
            'query' => $actionFeedQuery,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ]
            ],
        ]);
        $afDpSuccess->setTotalCount(10);

        $walletCount = Wallet::find()->where(['user_id'=>$user_id])->count();
        $walletTransactionCount = WalletTransaction::find()->where(['user_id'=>$user_id])->andWhere(['>','created_at',time()-86400*30])->count();
        $apiCallCount = UserApiLog::find()->where(['user_id'=>$user_id])->count();
        $volumeCount = \LNPay::$app->user->identity->getWalletAPIUsageByPeriod(strtotime('-30 days'),time());

        return $this->render('home',compact('afDp','afDpFailed','afDpSuccess','walletCount','walletTransactionCount','apiCallCount','volumeCount'));
    }

    public function actionSearch()
    {

    }

}
