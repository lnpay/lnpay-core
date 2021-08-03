<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model lnpay\node\models\LnNodeProfile */

$this->title = 'Create Ln Node Profile';
$this->params['breadcrumbs'][] = ['label' => 'Ln Node Profiles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ln-node-profile-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
