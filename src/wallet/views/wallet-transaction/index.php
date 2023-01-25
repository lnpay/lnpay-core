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
    <?php //$this->render('_search', ['model' => $searchModel]); ?>

    <?php
        $allowedFilter = [
                \lnpay\wallet\models\WalletTransactionType::LN_ROLL_UP,
                \lnpay\wallet\models\WalletTransactionType::LN_LNURL_PAY_OUTBOUND,
                \lnpay\wallet\models\WalletTransactionType::LN_LNURL_PAY_INBOUND,
                \lnpay\wallet\models\WalletTransactionType::LN_DEPOSIT,
                \lnpay\wallet\models\WalletTransactionType::LN_WITHDRAWAL,
                \lnpay\wallet\models\WalletTransactionType::LN_TRANSFER_OUT,
                \lnpay\wallet\models\WalletTransactionType::LN_TRANSFER_IN,
                \lnpay\wallet\models\WalletTransactionType::LN_LNURL_WITHDRAW,
                \lnpay\wallet\models\WalletTransactionType::LN_NETWORK_FEE,
        ];
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => [
            'class' => 'yii\bootstrap4\LinkPager'
        ],
        'columns' => [
            'created_at:datetime',
            [
                'header'=>'Transaction ID',
                'value'=>function (\lnpay\wallet\models\WalletTransaction $model) {
                    return Html::a($model->external_hash,['/wallet/wallet-transaction/view','id'=>$model->external_hash]);
                },
                'format'=>'raw'
            ],
            [
                'header'=>'Wallet ID',
                'value'=>function (\lnpay\wallet\models\WalletTransaction $model) {
                    return Html::a($model->wallet->external_hash,['/wallet/wallet/view','id'=>$model->wallet->external_hash]);
                },
                'format'=>'raw'
            ],
            [
                'header'=>'LnTx ID',
                'value'=>function (\lnpay\wallet\models\WalletTransaction $model) {
                    if ($model->ln_tx_id)
                        return Html::a($model->lnTx->external_hash,['/wallet/wallet-transaction/view','id'=>$model->external_hash]);
                    else
                        return NULL;
                },
                'format'=>'raw'
            ],
            [
                'header'=>'Type',
                'value'=>'walletTransactionType.display_name',
                'filter' => Html::activeDropDownList($searchModel,
                    'wtx_type_id',
                    \yii\helpers\ArrayHelper::map([null=>'All']+\lnpay\wallet\models\WalletTransactionType::find()->where(['id'=>$allowedFilter])->all(),'id','display_name')
                )
            ],
            'num_satoshis',
            'user_label',

            //'json_data',
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
