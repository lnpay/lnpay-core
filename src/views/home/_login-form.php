<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
?>

<section class="login" id="login-form-view">
    <br/>
    <div class="pricing-inner text-left">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'username')->textInput()->label('Email') ?>

        <?= $form->field($model, 'password')->passwordInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Login', ['class' => 'btn btn-primary login-btn', 'name' => 'login-button']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</section>

<div class="signin-footer login-footer">
    <div><?= Html::a('Forgot Password?', ['home/request-password-reset']) ?></div>
    <div>Don't have an account? <?= Html::a('Sign Up', ['home/signup'],['id'=>'login-goto-signup']) ?>.</div>
</div>
