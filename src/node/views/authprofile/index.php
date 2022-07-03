<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel lnpay\node\models\LnNodeProfileSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Macaroon Bakery';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ln-node-profile-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Bake Macaroon', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'user_label',
            'created_at:datetime',
            //'is_default',

            //'status_type_id',
            //'macaroon_hex:ntext',
            //'username',
            //'password',
            //'access_key',
            //'json_data',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
