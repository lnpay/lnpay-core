<?php /** @noinspection PhpUnhandledExceptionInspection */

use \yii\helpers\Html;
use \yii\helpers\HtmlPurifier;

echo \yii\grid\GridView::widget([
    'dataProvider' => $userDp,
    'summary' => '',
    //'tableOptions' => ['class' => ''],
    //'headerRowOptions' => ['class' => 'table-header'],
    'columns' => [
        'created_at:datetime',
        [
            'header'=>'Label',
            'contentOptions' => ['class'=>'','style'=>'width:125px'],
            'attribute'=>'user_label'
        ],
        [
            'header'=>'Start Balance',
            'contentOptions' => ['class' => ''],
            'value'=>function ($model) {
                return
                    "<span class='alert alert-success' style='white-space: nowrap;' role='alert'>" . $model->start_balance . " <span style='color: #566776; font-size: 12px;'>sats</span></span>";
            },
            'format' => 'raw'
        ],
        [
            'header'=>'Total Withdrawn',
            'contentOptions' => ['class' => ''],
            'value'=>function ($model) {
                return
                    "<span class='alert alert-danger' style='white-space: nowrap;' role='alert'>" . $model->total_withdrawn . " <span style='color: #566776; font-size: 12px;'>sats</span></span>";
            },
            'format' => 'raw'
        ],
        [
            'header'=>'Remaining Balance',
            'contentOptions' => ['class' => ''],
            'value'=>function ($model) {
                return
                    "<span class='alert alert-success' style='white-space: nowrap;' role='alert'>" . $model->remaining_balance . " <span style='color: #566776; font-size: 12px;'>sats</span></span>";
            },
            'format' => 'raw'
        ],
        [
            'header'=>'Status',
            'contentOptions' => ['class' => ''],
            'value'=>function ($model) {
                if($model->status_type_id === \lnpay\models\StatusType::FAUCET_ACTIVE) {
                    return "<span class='alert alert-success' role='alert'>{$model->statusType->display_name}</span>";
                } else {
                    return "<span class='alert alert-danger' role='alert'>{$model->statusType->display_name}</span>";
                }
            },
            'format' => 'raw'
        ],
        /*
        [
            'header'=>'Usage',
            'headerOptions' => ['style' => 'text-align: right;', 'class' => 'visible-sm visible-md visible-lg'],
            'contentOptions' => ['class' => 'number usage-data'],
            'value'=>function ($model) {

                return "<span style='white-space: nowrap;'>" . 1 . " <span style='color: #566776; font-size: 12px;'>clicks</span></span> <br />" .
                    "<span style='white-space: nowrap;'>" . 2 . " <span style='color: #566776; font-size: 12px;'>conversions </span></span> <br />" .
                    "<span style='white-space: nowrap;'>" . 3 . " <span style='color: #566776; font-size: 12px;'>PTR </span></span> <br />"
                    ;
            },
            'format'=>'raw'
        ],*/
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Actions',
            'template' => '{view}',
            //'headerOptions' => ['class' => 'visible-sm visible-md visible-lg'],
            'contentOptions' => ['style' => 'text-align: center;', 'class' => ''],
            'buttons' => [
                'view' => function ($url, $model) {
                    return Html::a('<span class="btn btn-info">Details <i class="fa fa-arrow-alt-circle-right"></i></span> ', ['/faucet-gen/view','id'=>$model->id], [
                        'title' => \LNPay::t('app', 'lead-update'),
                    ]);
                }
            ]
        ]
    ],
]); ?>