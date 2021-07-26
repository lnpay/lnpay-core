<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Your Account';
$this->beginContent('@app/views/layouts/sidebar/_nav-account.php');
?>
<h1>Account Details</h1>
<?php
    echo \yii\widgets\DetailView::widget([
        'model' => $userModel,
        'attributes' => [
                [
                        'label'=>'ID',
                        'value'=>function($model) { return $model->external_hash; }
                ],
                'created_at:datetime',
                'tz',
                'email',
        ]
    ]);
?>

<h3 style="margin-top: 2em; border-bottom: solid 1px #dddddd; padding-bottom: 10px;">Change Password</h3>
<?php
$form = ActiveForm::begin([
    'id' => 'changepassword-form',
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-lg-4\">
                            {input}</div>\n<div class=\"col-lg-5\">
                            {error}</div>",
        'labelOptions' => ['class' => 'col-lg-3 control-label'],
    ],
]); ?>
<?= $form->field($model, 'currentPassword', [
    'inputOptions' => [
        'placeholder' => 'Current Password',
        'class' => 'form-control'
    ]
])->passwordInput() ?>

<?= $form->field($model, 'newPassword', [
    'inputOptions' => [
        'placeholder' => 'New Password',
        'class' => 'form-control'
    ]
])->passwordInput() ?>

<?= $form->field($model, 'confirmNewPassword', [
    'inputOptions' => [
        'placeholder' => 'Confirm New Password',
        'class' => 'form-control'
    ]
])->passwordInput() ?>

<div class="form-group">
    <div class="col-lg-offset-3 col-lg-4">
        <?= Html::submitButton('Change password', [
            'class' => 'btn btn-primary btn-block'
        ]) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>

<?php
$this->endContent();
?>