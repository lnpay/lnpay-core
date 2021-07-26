<?php
namespace app\controllers;

use app\components\LNPayComponent;
use app\models\CustyDomain;
use Yii;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use \app\models\User;

/**
 * Home controller
 */
class BaseDashController extends Controller
{
    public function beforeAction($event)
    {
        parent::beforeAction($event);
        if (!Yii::$app->user->isGuest)
            LNPayComponent::processTz(Yii::$app->user->identity);

        if (!Yii::$app->user->identity->lnNode) {
            return $this->redirect('/node/dashboard/add');
        }

        return true;
    }

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


}