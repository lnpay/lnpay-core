<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\integration\IntegrationWebhook */

$this->title = 'Webhooks - '.$model->external_hash;
$this->params['breadcrumbs'][] = ['label' => 'Webhooks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<?php $this->beginContent('@app/views/layouts/sidebar/_nav-developers.php');?>
<div class="integration-webhook-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_requests',compact('model')) ?>

</div>
<?php $this->endContent();?>