<?php
$this->title = "Access Keys: ".$wallet->user_label;
$this->params['breadcrumbs'][] = ['label' => 'Wallets', 'url' => ['/wallet/dashboard']];
$this->params['breadcrumbs'][] = ['label' => $wallet->user_label, 'url' => ['/wallet/view','id'=>$wallet->external_hash]];
$this->params['breadcrumbs'][] = 'Access Keys';
?>

<?php $this->beginContent('@app/wallet/views/layouts/_nav-wallets.php',compact('wallet')); ?>

<h1>Wallet Access Keys <?=\yii\helpers\Html::a('API Docs <i class="fa fa-external-link-alt"></i>','https://docs.lnpay.co',[
        'class'=>'btn btn-primary',
        'target'=>'_blank',
        'title'=>'Use the API for basic functionality using the permissioned keys below.',
        'data-toggle' => 'tooltip',
        'data-placement' => 'right']);?>
</h1>
<?php

$items=[];
$items[] = [
    'label' => 'Wallet ID',
    'value'=> $wallet->publicId,
    'format'=>'raw'
];
foreach ($wallet->getUserAccessKeys() as $roleName => $keys) {
    $str = '';
    foreach ($keys as $k) {
        $str.=$k.'<br/>';
    }
    $items[] = [
        'label' => $roleName,
        'value'=> $str,
        'format'=>'raw'
    ];
}
echo \yii\widgets\DetailView::widget([
    'model' => $wallet->getUserAccessKeys(),
    'attributes' => $items
]);
?>

<?php $this->endContent(); ?>
