<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

$this->title = 'My Wallets';
$this->params['breadcrumbs'][] = $this->title;

?>
    <div class="paywalls-header">
        <h2 style="margin-top: 0px;">Advanced Wallets <?=Html::a('API Docs <i class="fa fa-external-link-alt"></i>','https://docs.lnpay.co',[
                'class'=>'btn btn-primary',
                'target'=>'_blank',
                'title'=>'Use the API for basic functionality using the permissioned keys below.',
                'data-toggle' => 'tooltip',
                'data-placement' => 'right']);?></h2>
        <a href="/wallet/create"><button class="styled-button-success">Create Wallet <i class="fa fa-plus-circle"></i></button></a>
    </div>

<?php Pjax::begin(); ?>
<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        //'id',
        'created_at:datetime',
        'user_label',
        'balance',
        [
            'attribute'=>'walletType.display_name',
            'header'=>'Wallet Type',
            'filter' => Html::activeDropDownList($searchModel, 'wallet_type_id', \yii\helpers\ArrayHelper::map([null=>'All']+\lnpay\wallet\models\WalletType::getAvailableWalletTypes(),'id','display_name'))
        ],

        //'lnNode.alias',
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
                    return Html::a('<span class="btn btn-info">Details <i class="fa fa-arrow-alt-circle-right"></i></span> ', ['/wallet/view','id'=>$model->publicId], [
                        'title' => \LNPay::t('app', 'lead-update'),
                        'data-pjax'=>0
                    ]);
                }
            ]
        ]
    ],
]); ?>

<?php Pjax::end(); ?>