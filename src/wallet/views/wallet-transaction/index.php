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

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Wallet Transaction', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'created_at',
            'updated_at',
            'user_id',
            'wallet_id',
            //'num_satoshis',
            //'ln_tx_id',
            //'user_label',
            //'external_hash',
            //'json_data',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
