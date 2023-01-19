<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model lnpay\models\integration\IntegrationWebhook */

$this->title = 'Create Webhook';
$this->params['breadcrumbs'][] = ['label' => 'Integration Webhooks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="integration-webhook-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
