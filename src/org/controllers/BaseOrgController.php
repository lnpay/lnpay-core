<?php

namespace lnpay\org\controllers;

use lnpay\base\DashController;
use lnpay\node\models\LnNode;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;



class BaseOrgController extends DashController
{
    public $orgObject;

    public function beforeAction($event)
    {
        if (parent::beforeAction($event)) {
            $user = \LNPay::$app->user->identity;
            $this->orgObject = $user->org;

            \LNPay::$app->getView()->params['breadcrumbs'][] = ['label' => $this->orgObject->display_name, 'url' => ['view']];

            return true;
        }

    }
}
