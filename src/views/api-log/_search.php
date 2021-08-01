<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model lnpay\models\UserApiLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-api-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'created_at') ?>

    <?= $form->field($model, 'external_hash') ?>

    <?= $form->field($model, 'api_key') ?>

    <?php // echo $form->field($model, 'ip_address') ?>

    <?php // echo $form->field($model, 'sdk') ?>

    <?php // echo $form->field($model, 'method') ?>

    <?php // echo $form->field($model, 'base_url') ?>

    <?php // echo $form->field($model, 'request_path') ?>

    <?php // echo $form->field($model, 'request_body') ?>

    <?php // echo $form->field($model, 'request_headers') ?>

    <?php // echo $form->field($model, 'status_code') ?>

    <?php // echo $form->field($model, 'response_body') ?>

    <?php // echo $form->field($model, 'response_headers') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
