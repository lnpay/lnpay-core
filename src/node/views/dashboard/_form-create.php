<?php

use lnpay\node\models\LnNode;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;


?>

    <div class="container">
        <div class="ln-node-form col-md-6">
            <?php echo Html::errorSummary($model); ?>
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'user_label')->textInput()->hint('For your reference') ?>
            <?= $form->field($model, 'implementation')->dropDownList(['LND'=>'LND']) ?>
            <?= $form->field($model, 'network')->dropDownList(['testnet'=>'testnet']) ?>
            <?= $form->field($model, 'lnd_version')->dropDownList(['v0.10.99-beta'=>'v0.10.99-beta']) ?>
            <?php echo Html::submitButton('Launch!', ['id'=>'create-node-submit-button','class' => 'btn btn-info']); ?>
            <?php ActiveForm::end(); ?>

            <br/><br/>

        </div>

    </div>