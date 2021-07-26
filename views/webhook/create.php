<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\integration\IntegrationWebhook */

$this->title = 'Create Webhook';
$this->params['breadcrumbs'][] = ['label' => 'Integration Webhooks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php $this->beginContent('@app/views/layouts/sidebar/_nav-developers.php');?>
<div class="integration-webhook-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php $this->endContent(); ?>