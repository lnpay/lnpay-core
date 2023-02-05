<?php

use \yii\helpers\Html;
$this->title = "Transactions: ".$wallet->user_label;
$this->params['breadcrumbs'][] = ['label' => 'Wallets', 'url' => ['/wallet/dashboard']];
$this->params['breadcrumbs'][] = ['label' => $wallet->user_label, 'url' => ['/wallet/view','id'=>$wallet->external_hash]];
$this->params['breadcrumbs'][] = 'Transactions';
?>
<?php $this->beginContent('@app/wallet/views/layouts/_nav-wallets.php',compact('wallet')); ?>
<?php
echo \yii\grid\GridView::widget([
    'dataProvider' => $wtxDataProvider,
    'filterModel' => $wtxSearchModel,
    'summary' => '',
    'tableOptions' => ['class' => 'table'],
    'headerRowOptions' => ['class' => 'table-header'],
    'pager' => [
        'class' => 'yii\bootstrap4\LinkPager'
    ],
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
            'header'=>'Transaction Type',
            'attribute'=>function ($model) {
                if ($model->wallet->isFeeWallet)
                    return $model->walletTransactionType->display_name."<br/>Ref: ".$model->getJsonData('source_transaction');
                else
                    return $model->walletTransactionType->display_name."<br/>".$model->external_hash;
            },
            'format'=>'raw'
        ],
        'num_satoshis',
        'user_label'

    ],
]); ?>

<?php $this->endContent();?>
