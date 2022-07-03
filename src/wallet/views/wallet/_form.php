<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model lnpay\wallet\models\Wallet */
/* @var $form yii\widgets\ActiveForm */

$user = \LNPay::$app->user->identity;
?>

<div class="wallet-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_label')->textInput(['maxlength' => true]) ?>

    <?php
    $opts = [];
    if ($model->id) {
        $opts = ['disabled'=>true];
    }
    $types = \yii\helpers\ArrayHelper::map(\lnpay\wallet\models\WalletType::getAvailableWalletTypes(),'id','display_name');


    $q = $user->getLnNodeQuery()->all();
    echo $form->field($model, 'ln_node_id')->dropDownList(\yii\helpers\ArrayHelper::map($q,'id',function ($node){return $node->alias." ({$node->org->display_name})";}),$opts);




    ?>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
