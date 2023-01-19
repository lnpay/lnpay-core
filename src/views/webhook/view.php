<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model lnpay\models\integration\IntegrationWebhook */

$this->title = 'Webhooks - '.$model->external_hash;
$this->params['breadcrumbs'][] = ['label' => 'Webhooks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="integration-webhook-view">


    <?= $this->render('_requests',compact('model')) ?>

</div>
