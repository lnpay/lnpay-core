<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model lnpay\node\models\LnNodeProfile */

$this->title = 'Update Ln Node Profile: ' . $model->user_label;
$this->params['breadcrumbs'][] = ['label' => 'Ln Node Profiles', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="ln-node-profile-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
