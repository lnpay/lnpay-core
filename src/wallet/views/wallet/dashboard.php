<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

$this->title = 'Wallets';
$this->params['breadcrumbs'][] = $this->title;

?>
    <div class="paywalls-header">
        <a href="create"><button class="btn btn-primary">Create Wallet <i class="fa fa-plus-circle"></i></button></a>
    </div>

<?php Pjax::begin(); ?>
<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'pager' => [
        'class' => 'yii\bootstrap4\LinkPager'
    ],
    'columns' => [
        [
            'attribute'=>'external_hash',
            'header'=>'ID'
        ],
        'created_at:datetime',
        'user_label',
        'balance',
        /*[
            'attribute'=>'walletType.display_name',
            'header'=>'Wallet Type',
            'filter' => Html::activeDropDownList($searchModel, 'wallet_type_id', \yii\helpers\ArrayHelper::map([null=>'All']+\lnpay\wallet\models\WalletType::getAvailableWalletTypes(),'id','display_name'))
        ],*/

        /*[
            'attribute'=>'lnNode.alias',
            'header'=>'LN Node',
            'filter' => Html::activeDropDownList($searchModel, 'ln_node_id', \yii\helpers\ArrayHelper::map([null=>'All']+\LNPay::$app->user->identity->lnNodes,'id','alias'))
        ],*/
        //'external_hash',
        //'json_data',

        [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Actions',
            'template' => '{view}',
            //'headerOptions' => ['class' => 'visible-sm visible-md visible-lg'],
            'contentOptions' => ['style' => 'text-align: center;', 'class' => ''],
            'buttons' => [
                'view' => function ($url, $model) {
                    return Html::a('<span class="btn btn-info">Details <i class="fa fa-arrow-alt-circle-right"></i></span> ', ['view','id'=>$model->publicId], [
                        'title' => \LNPay::t('app', 'lead-update'),
                        'data-pjax'=>0
                    ]);
                }
            ]
        ]
    ],
]); ?>

<?php Pjax::end(); ?>