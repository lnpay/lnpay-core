<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel lnpay\models\integration\IntegrationWebhookSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Webhooks';
$this->params['breadcrumbs'][] = $this->title;



?>
<?php $this->beginContent('@app/views/layouts/sidebar/_nav-developers.php');?>

<?php
echo \yii\bootstrap\Alert::widget([
    'body' => 'Please see '.Html::a('Webhooks: Getting Started','https://docs.lnpay.co/webhooks/getting-started',['target'=>'_blank']).' for more info',
    'options' => [
        'class' => 'alert-info',
    ],
]);

?>
<div class="integration-webhook-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Webhook', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}{update}{delete}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('<span class="btn btn-primary glyphicon glyphicon-eye-open"></span>', ['view','id'=>$model->external_hash], ['title' => 'Edit']);
                    },
                    'update' => function ($url, $model, $key) {
                        return Html::a('<span class="btn btn-info glyphicon glyphicon-pencil"></span>', ['update','id'=>$model->external_hash], ['title' => 'Edit']);
                    },
                    'delete' => function ($url, $model, $key) {
                        return Html::a('<span class="btn btn-danger glyphicon glyphicon-trash"></span>', ['delete','id'=>$model->external_hash], [
                            'title' => 'Delete',
                            'data-confirm' => \LNPay::t('yii', 'Are you sure you want to delete this item?'),
                            'data-method' => 'post'
                        ]);
                    },
                ],
            ],
            [
                'format'=>'raw',
                'attribute' => 'action_name_id',
                'value'=>function($model) { return implode('<br/>',$model->action_name_id); },
                'header'=>'Actions'
            ],
            //'integration_service_id',
            //'secret',
            //'http_method',
            //'content_type',
            'endpoint_url',
            'statusType.display_name',
            //'external_hash',
            //'json_data:ntext',
            //'created_at',
            //'updated_at',


        ],
    ]); ?>


</div>
<?php $this->endContent();?>