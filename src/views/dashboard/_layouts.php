<?php /** @noinspection PhpUnhandledExceptionInspection */

use \yii\helpers\Html;
$this->title = 'Custom CSS Layouts';
?>
<p>Add custom CSS Layouts to your paywalls!</p>
<?php
echo \yii\grid\GridView::widget([
    'dataProvider' => $layoutDp,
    'summary' => '',
    'tableOptions' => ['class' => 'table paywalls-table'],
    'headerRowOptions' => ['class' => 'table-header'],
    'columns' => [
        [
            'header'=>'ID',
            'attribute'=>'external_hash'
        ],
        [
            'header'=>'Label',
            'attribute'=>'label'
        ],
        [
            'header'=>'CSS',
            'attribute'=>'css'
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Actions',
            'template' => '{update}{delete}',
            'buttons' => [
                'update' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-pencil"></span> ', ['/user-layout/update','id'=>$model->id], [
                        'title' => \LNPay::t('app', 'lead-update'),
                    ]);
                },
                'delete' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-trash"></span> ', ['/user-layout/delete','id'=>$model->id], [
                        'data-confirm' => \LNPay::t('yii', 'Are you sure you want to delete this layout?'),
                        'data-method' => 'post',
                        'title'=>\LNPay::t('app', 'lead-delete'),
                    ]);
                }

            ]
        ]

    ],
]); ?>