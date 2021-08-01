<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model lnpay\models\LnNodeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ln-node-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'alias') ?>

    <?= $form->field($model, 'ln_node_implementation_id') ?>

    <?= $form->field($model, 'default_pubkey') ?>

    <?= $form->field($model, 'uri') ?>

    <?php // echo $form->field($model, 'host') ?>

    <?php // echo $form->field($model, 'rpc_port') ?>

    <?php // echo $form->field($model, 'rest_port') ?>

    <?php // echo $form->field($model, 'ln_port') ?>

    <?php // echo $form->field($model, 'tls_cert') ?>

    <?php // echo $form->field($model, 'getinfo') ?>

    <?php // echo $form->field($model, 'status_type_id') ?>

    <?php // echo $form->field($model, 'rpc_status') ?>

    <?php // echo $form->field($model, 'rest_status') ?>

    <?php // echo $form->field($model, 'json_data') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
