<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \lnpay\models\CustyDomain */

?>
    <h3>Add Domain</h3>
<?php
$form = \yii\widgets\ActiveForm::begin([
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-lg-4\">
                            {input}</div>\n<div class=\"col-lg-5\">
                            {error}</div>{hint}",
        'labelOptions' => ['class' => 'col-lg-3 control-label'],
    ],
]); ?>
<?= $form->field($model, 'domain_name', [
    'inputOptions' => [
            'placeholder'=>'e.g. mydomain.com',
        'class' => 'form-control',
    ]
])->textInput()->hint('This is Lightning Address domain name') ?>

<?= $form->field($model, 'display_name', [
    'inputOptions' => [
        'class' => 'form-control',
        'placeholder'=>'e.g. My LN Address domain'
    ]
])->textInput() ?>

    <div class="form-group">
        <div class="col-lg-offset-3 col-lg-4">
            <?= Html::submitButton('Add Domain', [
                'class' => 'btn btn-primary btn-block'
            ]) ?>
        </div>
    </div>
<?php \yii\widgets\ActiveForm::end(); ?>

