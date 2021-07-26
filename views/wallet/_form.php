<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\wallet\Wallet */
/* @var $form yii\widgets\ActiveForm */

$user = Yii::$app->user->identity;
?>

<div class="wallet-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_label')->textInput(['maxlength' => true]) ?>

    <?php
    $opts = [];
    if ($model->id) {
        $opts = ['disabled'=>true];
    }
    $types = \yii\helpers\ArrayHelper::map(\app\models\wallet\WalletType::getAvailableWalletTypes(),'id','display_name');

    if ($user->lnNode) { //user has at least 1 node!
        $q = $user->getLnNodeQuery()->all();
        echo $form->field($model, 'ln_node_id')->dropDownList(\yii\helpers\ArrayHelper::map($q,'id','alias'),$opts);
    } else {
        $custodialNode = \app\modules\node\models\LnNode::getLnpayNodeQuery()->one();
        echo $form->field($model, 'ln_node_id')->dropDownList([$custodialNode->id=>$custodialNode->alias.' (CUSTODIAL)'],$opts);
    }



    ?>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
