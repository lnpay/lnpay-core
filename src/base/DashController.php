<?php
namespace lnpay\base;

use lnpay\components\LNPayComponent;
use lnpay\models\CustyDomain;
use Yii;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use \lnpay\models\User;


/**
 * Home controller
 */
class DashController extends Controller
{
    public function beforeAction($event)
    {
        parent::beforeAction($event);

        if (!\LNPay::$app->user->isGuest)
            LNPayComponent::processTz(\LNPay::$app->user->identity);

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