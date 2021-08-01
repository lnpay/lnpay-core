<?=$this->render('__base_sidebar'); ?>

<div class="sidebar-layout">
    <div class="sidebar">
        <h2 class="text-center"><?=$wallet->user_label;?></h2>
        <br/>
        <?=\yii\helpers\Html::a('<img src="/img/icons/wallet.svg" style="height: 25px" /> Send/Receive/Transfer',['wallet/view','id'=>$wallet->external_hash],['class'=>'sidebar-item']);?>
        <?=\yii\helpers\Html::a('<img src="/img/icons/rpc-forwarder.svg" style="height: 25px" />Keysend',['wallet/keysend','id'=>$wallet->external_hash],['class'=>'sidebar-item']);?>
        <?=\yii\helpers\Html::a('<img src="/img/icons/keys-auth.svg" style="height: 25px" />Access Keys',['wallet/access-keys','id'=>$wallet->external_hash],['class'=>'sidebar-item']);?>
        <?=\yii\helpers\Html::a('<img src="/img/icons/events.svg" style="height: 25px" /> Transactions',['wallet/transactions','id'=>$wallet->external_hash],['class'=>'sidebar-item']);?>
        <?=\yii\helpers\Html::a('<img src="/img/icons/nodes.svg" style="height: 25px" />LN Node: '.$wallet->lnNode->alias,['wallet/ln-node','id'=>$wallet->external_hash],['class'=>'sidebar-item']);?>

    </div>
    <div>
        <?=$content;?>
    </div>
</div>


