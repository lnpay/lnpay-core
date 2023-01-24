<?php
/* @var  \lnpay\wallet\models\Wallet $wallet */
$this->title = "Keysend: ".$wallet->user_label;
$this->params['breadcrumbs'][] = ['label' => 'Wallets', 'url' => ['/wallet/dashboard']];
$this->params['breadcrumbs'][] = ['label' => $wallet->user_label, 'url' => ['/wallet/view','id'=>$wallet->external_hash]];
$this->params['breadcrumbs'][] = 'Keysend';

?>

<?php $this->beginContent('@app/wallet/views/layouts/_nav-wallets.php',compact('wallet')); ?>
<h1>Keysend Logs</h1>
<?php
$cmd = 'lncli sendpayment -d '.$wallet->lnNode->default_pubkey.' -a [NUM_SATOSHIS] --keysend --data 696969='.bin2hex($wallet->external_hash);

switch ($wallet->wallet_type_id) {
    case \lnpay\wallet\models\WalletType::GENERIC_WALLET:
        echo \yii\bootstrap4\Alert::widget([
            'body' => 'To KEYSEND to via LND to THIS WALLET run the command below: <br/><br/>'.$cmd,
            'options' => [
                'id' => 'id-keysend-yay',
                'class' => 'alert-info',
            ]
        ]);
        break;
}
?>

<?php
echo \yii\grid\GridView::widget([
    'dataProvider' => $wtxDataProvider,
    'summary' => '',
    'tableOptions' => ['class' => 'table'],
    'headerRowOptions' => ['class' => 'table-header'],
    'columns' => [
        [
            'header'=>'Time',
            'attribute'=>function ($model) {
                return date(' g:i:s A T',$model->created_at) . '<br />' .
                    date('M j, Y',$model->created_at);
            },
            'format'=>'raw'
        ],
        'num_satoshis',
        [
            'header'=>'Data',
            'value'=>function ($model) {
                return \yii\helpers\VarDumper::export($model->lnTx->custom_records);
            },
            'format'=>'raw'
        ],

    ],
]);
?>

<?php $this->endContent(); ?>
