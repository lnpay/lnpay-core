<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\integration\IntegrationWebhook */

$this->title = 'Update Integration Webhook: ' . $model->external_hash;
$this->params['breadcrumbs'][] = ['label' => 'Integration Webhooks', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->external_hash, 'url' => ['view', 'id' => $model->external_hash]];
$this->params['breadcrumbs'][] = 'Update';
?>
<?php $this->beginContent('@app/views/layouts/sidebar/_nav-developers.php');?>
<div class="integration-webhook-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php $this->endContent();?>