<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\UserApiLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'API Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php $this->beginContent('@app/views/layouts/sidebar/_nav-developers.php');?>
<div class="user-api-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Actions',
                'template' => '{view}',
            ],
            'created_at:datetime',
            'status_code',
            'method',
            'request_path',
            //'external_hash',
            //'api_key',
            //'ip_address',
            //'sdk',
            //'method',
            //'base_url:url',
            //'request_path',
            //'request_body:ntext',
            //'request_headers:ntext',
            //'status_code',
            //'response_body:ntext',
            //'response_headers:ntext',


        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
<?php $this->endContent();?>