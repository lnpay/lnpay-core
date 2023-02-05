<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \lnpay\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
?>

<section class="login" id="signup-form-view">
    <br/>
    <div class="pricing-inner text-left">
        <?php $form = ActiveForm::begin(); ?>

        <?php //echo $form->errorSummary($model); ?>

        <?= $form->field($model, 'email')->label('Email'); ?>

        <?= $form->field($model, 'password')->passwordInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Create Account', ['class' => 'btn btn-primary login-btn', 'name' => 'signup-button']) ?>
        </div>
        <?php ActiveForm::end(); ?>
</section>

<div class="signin-footer signup-footer">
    <div>Already signed up? <?=Html::a('Log in',['/home/login'],['id'=>'signup-goto-login']);?></div>
</div>