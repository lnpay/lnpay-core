<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
?>
<div class="site-login">
    <div class="site-login-container">
        <h2 class="pricing-title">Log in to ⚡LNPay</h2>
        <?=$this->render('_login-form',compact('model'));?>
    </div>
</div>