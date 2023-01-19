<?php
    $this->title = 'Change Password';
    $this->beginContent('@app/views/layouts/sidebar/_nav-account.php');
?>
<?php
$form = \yii\widgets\ActiveForm::begin([
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
        <?= \yii\helpers\Html::submitButton('Change password', [
            'class' => 'btn btn-primary btn-block'
        ]) ?>
    </div>
</div>
<?php \yii\widgets\ActiveForm::end(); ?>

<?php $this->endContent(); ?>
