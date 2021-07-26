<?php

use vxm\mfa\QrCodeWidget;
$this->title = 'Multi-factor Authentication';
$this->beginContent('@app/views/layouts/sidebar/_nav-settings.php');

?>

<h1>MFA</h1>

<div class="well">
    <?php
    if (Yii::$app->user->identity->mfaSecretKey) {

        echo QrCodeWidget::widget([
            'label' => Yii::$app->user->identity->email,
            'issuer' => Yii::$app->name
        ]);
    } else {
        echo \yii\helpers\Html::a('Turn on MFA');
    }

    ?>
</div>



<?php
$this->endContent();

?>