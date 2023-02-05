<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model lnpay\models\log\UserApiLog */

$this->title = $model->external_hash;
$this->params['breadcrumbs'][] = ['label' => 'User Api Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<div class="user-api-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'external_hash',
            'created_at:datetime',
            'ip_address',
            'sdk',
            'method',
            'base_url',
            'request_path',
            [
                'attribute'=>'request_body',
                'value'=>function($model) { return "<pre><code>{$model->request_body}</code></pre>"; },
                'format'=>'raw'
            ],
            //'request_headers:ntext',
            'status_code',
            [
                'attribute'=>'response_body',
                'value'=>function($model) { return "<pre><code>{$model->response_body}</code></pre>"; },
                'format'=>'raw'
            ],
        ],
    ]) ?>

</div>
