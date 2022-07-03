<?php

namespace lnpay\org\controllers;

use lnpay\models\ChangePasswordForm;
use lnpay\models\User;
use lnpay\org\models\Org;
use lnpay\org\models\OrgUserType;
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
        if (\LNPay::$app->user->identity->org_user_type_id != OrgUserType::TYPE_OWNER) {
            \LNPay::$app->session->setFlash('error','Must be org owner!');
            return $this->redirect('view');
        }


        $dataProvider = new ActiveDataProvider([
            'query' => $this->orgObject->getUsers()
        ]);

        return $this->render('members', [
            'org' => $this->orgObject,
            'dataProvider'=>$dataProvider
        ]);
    }
}
