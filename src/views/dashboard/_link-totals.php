<?php /** @noinspection PhpUnhandledExceptionInspection */

use \yii\helpers\Html;
use \yii\helpers\HtmlPurifier;

echo \yii\grid\GridView::widget([
    'dataProvider' => $userDp,
    'summary' => '',
    'tableOptions' => ['class' => 'table paywalls-table'],
    'headerRowOptions' => ['class' => 'table-header'],
    'columns' => [
        [
            'header'=>'Memo',
            'contentOptions' => ['class'=>'memo-data','style'=>'width:125px'],
            'value'=>function ($model) {
                return
                    "<span class='table-memo'>" . HtmlPurifier::process($model->memo?:"<em style='color:grey;'>(no memo)</em>") . "</span> <br />" .
                    Html::a($model->getUrl(),$model->getUrl(),['target'=>'_blank', "class"=>"view-paywall-link", "style"=>"font-size: 12px;"]) . " -> " . Html::a(HtmlPurifier::process($model->destination_url),$model->destination_url,['target'=>'_blank', "style"=>"font-size: 12px;"]);
            },
            'format'=>'raw'
        ],
        [
            'header'=>'',
            'contentOptions' => ['class' => 'number success-data'],
            'value'=>function ($model) {
                return
                "<span class='alert alert-success' style='white-space: nowrap;' role='alert'>" . $model->totalSettledSats . " <span style='color: #566776; font-size: 12px;'>sats earned</span></span>";
            },
            'format' => 'raw'
        ],
        [
            'header'=>'',
            'contentOptions' => ['class' => 'layout-data'],
            'value'=>function ($model) {
                return
                    "<span class='alert alert-info' role='alert'>" . (HtmlPurifier::process(@$model->linkType->display_name)?:'Default') . "</span>";
            },
            'format' => 'raw'
        ],
        [
            'header'=>'Usage',
            'headerOptions' => ['style' => 'text-align: right;', 'class' => 'visible-sm visible-md visible-lg'],
            'contentOptions' => ['class' => 'number usage-data'],
            'value'=>function ($model) {

                return "<span style='white-space: nowrap;'>" . $model->overallPaywallStats['clicks'] . " <span style='color: #566776; font-size: 12px;'>clicks</span></span> <br />" .
                "<span style='white-space: nowrap;'>" . $model->overallPaywallStats['conversions'] . " <span style='color: #566776; font-size: 12px;'>conversions </span></span> <br />" .
                "<span style='white-space: nowrap;'>" . $model->overallPaywallStats['ptr'] . " <span style='color: #566776; font-size: 12px;'>PTR </span></span> <br />"
                ;
            },
            'format'=>'raw'
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Actions',
            'template' => '{view}',
            'headerOptions' => ['class' => 'visible-sm visible-md visible-lg'],
            'contentOptions' => ['style' => 'text-align: center;', 'class' => 'action-data'],
            'buttons' => [
                'view' => function ($url, $model) {
                    switch ($model->link_type_id) {
                        case \lnpay\models\link\Type::TYPE_POS:
                            $t = '/link/view-pos';
                            break;
                        default:
                            $t = '/link/view';
                    }
                    return Html::a('<span class="btn btn-info">Details <i class="fa fa-arrow-alt-circle-right"></i></span> ', [$t,'id'=>$model->id], [
                        'title' => \LNPay::t('app', 'lead-update'),
                    ]);
                }
            ]
        ]
    ],
]); ?>