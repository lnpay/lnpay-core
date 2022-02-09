<?php

namespace lnpay\org\controllers;

use lnpay\models\ChangePasswordForm;
use lnpay\models\User;
use lnpay\org\models\Org;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;



class HomeController extends BaseOrgController
{
    public function actionView()
    {
        return $this->render('index', [
            'org' => $this->orgObject,
        ]);
    }

    public function actionMembers()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => $this->orgObject->getUsers()
        ]);

        return $this->render('members', [
            'org' => $this->orgObject,
            'dataProvider'=>$dataProvider
        ]);
    }
}
