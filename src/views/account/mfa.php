<?php

use vxm\mfa\QrCodeWidget;
$this->title = 'Multi-factor Authentication';
$this->beginContent('@app/views/layouts/sidebar/_nav-settings.php');

?>

<h1>MFA</h1>

<div class="well">
    <?php
    if (\LNPay::$app->user->identity->mfaSecretKey) {

        echo QrCodeWidget::widget([
            'label' => \LNPay::$app->user->identity->email,
            'issuer' => \LNPay::$app->name
        ]);
    } else {
        echo \yii\helpers\Html::a('Turn on MFA');
    }

    ?>
</div>



<?php
$this->endContent();

?>