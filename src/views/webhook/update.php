<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model lnpay\models\integration\IntegrationWebhook */

$this->title = 'Update Integration Webhook: ' . $model->external_hash;
$this->params['breadcrumbs'][] = ['label' => 'Integration Webhooks', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->external_hash, 'url' => ['view', 'id' => $model->external_hash]];
$this->params['breadcrumbs'][] = 'Update';
?>

<div class="integration-webhook-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

