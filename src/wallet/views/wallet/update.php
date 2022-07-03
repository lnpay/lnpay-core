<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model lnpay\wallet\models\Wallet */

$this->title = 'Update Wallet: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Wallets', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="wallet-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
