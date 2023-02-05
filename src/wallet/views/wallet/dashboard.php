<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

$this->title = 'Wallets';
$this->params['breadcrumbs'][] = $this->title;

?>
<?php /* ?>
    <div class="row mb-3">
        <div class="col-xl-3 col-sm-6 py-2">
            <div class="card bg-success text-white h-100">
                <div class="card-body bg-success">
                    <div class="rotate">
                        <i class="fa fa-wallet fa-4x"></i>
                    </div>
                    <h6 class="text-uppercase">Total Wallets</h6>
                    <h1 class="display-4"></h1>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 py-2">
            <div class="card text-white bg-danger h-100">
                <div class="card-body bg-danger">
                    <div class="rotate">
                        <i class="fa fa-list fa-4x"></i>
                    </div>
                    <h6 class="text-uppercase">Transactions (30 days)</h6>
                    <h1 class="display-4"></h1>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 py-2">
            <div class="card text-white bg-info h-100">
                <div class="card-body bg-info">
                    <div class="rotate">
                        <i class="fa fa-twitter fa-4x"></i>
                    </div>
                    <h6 class="text-uppercase">Inbound Volume (30 days)</h6>
                    <h1 class="display-6"> Sats</h1>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 py-2">
            <div class="card text-white bg-warning h-100">
                <div class="card-body">
                    <div class="rotate">
                        <i class="fa fa-share fa-4x"></i>
                    </div>
                    <h6 class="text-uppercase">Outbound Volume (30 days)</h6>
                    <h1 class="display-6"> Sats</h1>
                </div>
            </div>
        </div>
    </div>
 <?php */ ?>
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

        [
            'attribute'=>'lnNode.alias',
            'header'=>'LN Node',
            'filter' => Html::activeDropDownList($searchModel, 'ln_node_id', \yii\helpers\ArrayHelper::map([null=>'All']+\LNPay::$app->user->identity->lnNodes,'id','alias'))
        ],
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