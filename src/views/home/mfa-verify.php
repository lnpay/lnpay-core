<?php

/**
 * @var \vxm\mfa\OtpForm $model
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin();

echo Html::tag('h1', 'Multi factor authenticate');

echo $form->field($model, 'otp');

echo Html::submitButton('Verify');

ActiveForm::end();

?>