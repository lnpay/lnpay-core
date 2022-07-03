<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model lnpay\node\models\LnNodeProfile */

$this->title = $model->user_label;
$this->params['breadcrumbs'][] = ['label' => 'Ln Node Profiles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="ln-node-profile-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'created_at:datetime',
            'ln_node_id',
            'is_default',
            'user_label'
        ],
    ]) ?>
    <pre><code><?=str_replace(' ','<br/>',$model->getMacaroonObject()->readableString());?></code></pre>
    <pre><code><?=str_replace(' ','<br/>',$model->getMacaroonObject()->hex);?></code></pre>


</div>
