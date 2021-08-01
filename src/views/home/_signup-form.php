<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
?>

<!--<div class="signin-header signup-header">-->
<!--    <div>Create your account to get started!</div>-->
<!--</div>-->
<section class="login" id="signup-form-view">
    <br/>
    <div class="pricing-inner text-left">
        <?php $form = ActiveForm::begin(['id' => 'form-signup','action'=>'/home/signup']);
        ?>

        <?php //$form->errorSummary($model); ?>

        <?php //$form->field($model, 'username')->textInput(['autofocus' => true]) ?>

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