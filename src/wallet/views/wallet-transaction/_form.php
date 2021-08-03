<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model lnpay\wallet\models\WalletTransaction */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wallet-transaction-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'wallet_id')->textInput() ?>

    <?= $form->field($model, 'num_satoshis')->textInput() ?>

    <?= $form->field($model, 'ln_tx_id')->textInput() ?>

    <?= $form->field($model, 'user_label')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'external_hash')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'json_data')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
