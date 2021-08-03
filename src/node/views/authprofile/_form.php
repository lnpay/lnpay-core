<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model lnpay\node\models\LnNodeProfile */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ln-node-profile-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
            $opts = [];
            if ($model->id) {
                $opts = ['disabled'=>true];
            }
    ?>

    <?php $this->registerJs('
        $("#admin_preset").on("click", function() {
            $(":checkbox").prop("checked",true);
            return false;
        });
        $("#readonly_preset").on("click", function() {
            $(":checkbox").prop("checked",false);
            $(":checkbox[value=onchain_read]").prop("checked",true);
            $(":checkbox[value=offchain_read]").prop("checked",true);
            $(":checkbox[value=address_read]").prop("checked",true);
            $(":checkbox[value=message_read]").prop("checked",true);
            $(":checkbox[value=peers_read]").prop("checked",true);
            $(":checkbox[value=info_read]").prop("checked",true);
            $(":checkbox[value=invoices]").prop("checked",true);
            $(":checkbox[value=signer]").prop("checked",true);
            return false;
        });
        
        $("#invoice_preset").on("click", function() {
            $(":checkbox").prop("checked",false);
            $(":checkbox[value=invoices_read]").prop("checked",true);
            $(":checkbox[value=invoices_write]").prop("checked",true);
            $(":checkbox[value=address_read]").prop("checked",true);
            $(":checkbox[value=address_write]").prop("checked",true);
            $(":checkbox[value=onchain_read]").prop("checked",true);
            return false;
        });
    
    '); ?>

    <?= $form->field($model, 'user_label')->textInput(['maxlength' => true]) ?>
    <div class="well">
        <h3>Presets</h3>
        <a href="#" id="admin_preset" class="btn btn-primary">admin</a> &nbsp;
        <a href="#" id="readonly_preset" class="btn btn-primary">readonly</a> &nbsp;
        <a href="#" id="invoice_preset" class="btn btn-primary">invoice</a> &nbsp;
    </div>

    <?= $form->field($model, 'submitted_perms')->checkboxList($model::getMacaroonCheckboxList(),
        ['separator' => '<br>']); ?>

    <div class="form-group">
        <?= Html::submitButton('Generate!', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
