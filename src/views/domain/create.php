<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model lnpay\models\integration\IntegrationWebhook */

$this->title = 'Create Domain';
$this->params['breadcrumbs'][] = ['label' => 'Domain', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php $this->beginContent('@app/views/layouts/sidebar/_nav-developers.php');?>
<div class="domain-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php $this->endContent(); ?>