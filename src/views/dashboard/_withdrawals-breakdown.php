<?php /** @noinspection PhpUnhandledExceptionInspection */

use \yii\helpers\Html;

echo \yii\grid\GridView::widget([
    'dataProvider' => $transactionQuery,
    'summary' => '',
    'tableOptions' => ['class' => 'table'],
    'headerRowOptions' => ['class' => 'table-header'],
    'columns' => [
        [
            'header'=>'Time',
            'attribute'=>function ($model) {
                return date(' g:i:s A T',$model->created_at) . '<br />' .
                    date('M j, Y',$model->created_at);
            },
            'format'=>'raw'
        ],
        [
            'header'=>'Amount',
            'value'=>function ($model) {
                return
                    "<span class='table-memo'>" . $model->num_satoshis . " sats</span> <br />";
            },
            'format'=>'raw'
        ],
        [
            'header'=>'Settled',
            'attribute'=>'settled',
            'value'=>function($model) {
                if($model->settled === 0) {
                    return "<span class='alert alert-danger' role='alert'>Unsettled</span>";
                } else {
                    return "<span class='alert alert-success' role='alert'>Settled</span>";
                }
            },
            'format'=>'raw'
        ]
    ],
]); ?>