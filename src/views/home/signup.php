<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
?>
<div class="site-login">
    <div class="site-login-container">
        <h2 class="pricing-title">Create your ⚡LNPAY Account</h2>
        <?=$this->render('_signup-form',compact('model'));?>
    </div>
</div>
