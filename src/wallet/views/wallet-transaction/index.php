<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel lnpay\wallet\models\WalletTransactionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Wallet Transactions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wallet-transaction-index">

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => [
            'class' => 'yii\bootstrap4\LinkPager'
        ],
        'columns' => [
            'external_hash',
            'created_at:datetime',
            'wallet.external_hash',
            'num_satoshis',
            'lnTx.external_hash',
            'user_label',

            //'json_data',
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
