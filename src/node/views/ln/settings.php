<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model lnpay\node\models\LnNode */

$this->title = $node->id;
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="ln-node-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p class="well">
        <h1>Delete Node</h1>
        <?= Html::a('Remove', ['delete', 'id' => $node->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this node? Cannot be undone!',
                'method' => 'post',
            ],
        ]) ?>
    </p>
</div>
