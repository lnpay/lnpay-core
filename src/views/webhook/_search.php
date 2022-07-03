<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model lnpay\models\integration\IntegrationWebhookSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="integration-webhook-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'external_hash') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'wallet_id') ?>

    <?php // echo $form->field($model, 'action_name_id') ?>

    <?php // echo $form->field($model, 'integration_service_id') ?>

    <?php // echo $form->field($model, 'secret') ?>

    <?php // echo $form->field($model, 'http_method') ?>

    <?php // echo $form->field($model, 'content_type') ?>

    <?php // echo $form->field($model, 'endpoint_url') ?>

    <?php // echo $form->field($model, 'status_type_id') ?>

    <?php // echo $form->field($model, 'json_data') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
