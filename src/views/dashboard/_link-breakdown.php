<?php /** @noinspection PhpUnhandledExceptionInspection */

use \yii\helpers\Html;
use \yii\helpers\HtmlPurifier;

echo \yii\grid\GridView::widget([
    'dataProvider' => $breakdownQuery,
    'filterModel'=>$breakdownSearch,
    'summary' => '',
    'tableOptions' => ['class' => 'table'],
    'headerRowOptions' => ['class' => 'table-header'],
    'columns' => [
        'id',
        [
            'header'=>'Time',
            'attribute'=>function ($model) {
                return date(' g:i:s A T',$model->updated_at) . '<br />' .
                date('M j, Y',$model->updated_at);
            },
            'format'=>'raw'
        ],
        'memo',
        [
            'header'=>'paid',
            'attribute'=>'settled',
            'value'=>function($model) {
                if($model->settled === 0) {
                    return "<span class='alert alert-danger' role='alert'>Unpaid</span>";
                } else {
                    return "<span class='alert alert-success' role='alert'>Paid</span>";
                }
            },
            'format'=>'raw',
            'filter'=>[0=>'Unpaid',1=>'Paid']
        ],
        [
            'header'=>'Referrer',
            'value'=>function ($model) {
                return HtmlPurifier::process($model->referrer);
            }
        ],
        [
            'header'=>'Custom data',
            'format'=>'raw',
            'value'=>function ($model) {
                $data = $model->getJsonData();
                $str = '';
                if (!empty($data)) {

                    foreach ($data as $key => $value) {
                        $str .= $key."=".$value."<br/>";
                        $str = HtmlPurifier::process($str);
                    }
                }
                return $str;
            }
        ],
    ],
]); ?>