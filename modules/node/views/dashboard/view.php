<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\node\models\LnNode */

$this->title = $model->id;
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="ln-node-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Remove', ['delete', 'id' => $model->id], [
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
            'alias',
            'ln_node_implementation_id',
            'default_pubkey',
            'uri',
            'host',
            'rpc_port',
            'rest_port',
            'ln_port',
            'tls_cert:ntext',
            'status_type_id',
            'rpc_status_id',
            'rest_status_id',
            'json_data',
        ],
    ]) ?>

</div>
