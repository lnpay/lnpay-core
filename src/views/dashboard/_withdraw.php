<?php

use \yii\bootstrap\ActiveForm;
use \yii\helpers\Html;

?>
    <?php if (\LNPay::$app->user->identity->eligibleToWithdraw) { ?>
        <?php $wForm = ActiveForm::begin([
            // 'layout'=>'horizontal',
            'enableAjaxValidation'=>true,
            'options'=>[
                'id'=>'withdrawForm',
                'class'=>'ajaxFormLoader'
            ],
            'validationUrl'=>'/dashboard/validate-withdrawal'
        ]); ?>
        <?php //$form->errorSummary($model); ?>

        <?= $wForm->field($wModel, 'invoice_request')->textArea(['placeholder'=>'lnbc10u1pwcxqfkpp5e9nu85e6fypp89ql0hnz7yj784jatyytugewmpwe9yqhla7zvg3sdqdfdshjsn92djk2cqzpgjuk3ljzzrqhwsfr36h0nnyzy3gx3sna3fdnj9pkcqakjnkly0cdhk0lagf763mtegeld78qdpwf7t52mvgxl3f8neuty8y0pvvvffjcp96x09n', 'rows'=>4]); ?>
            <?= Html::submitButton('Send ⚡', ['class' => 'styled-button-success','style'=>'white-space:unset;']) ?>

            <!-- Button trigger modal -->
            <button class="secondary-button" type="button" data-toggle="modal" data-target="#exampleModal" aria-expanded="false" aria-controls="manual-payment-info">
                View LNUrl QR Code
            </button>
        <?php ActiveForm::end(); ?>
    <?php } ?>