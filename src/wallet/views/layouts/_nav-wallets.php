<?php /*

<div class="sidebar-layout">
    <div class="sidebar">
        <h2 class="text-center"><?=$wallet->user_label;?></h2>
        <br/>
        <?=\yii\helpers\Html::a('<img src="/img/icons/wallet.svg" style="height: 25px" /> Send/Receive/Transfer',['wallet/view','id'=>$wallet->external_hash],['class'=>'sidebar-item']);?>
        <?php //\yii\helpers\Html::a('<img src="/img/icons/loop.svg" style="height: 25px" /> Loop Out',['wallet/loop','id'=>$wallet->external_hash],['class'=>'sidebar-item']);?>
        <?=\yii\helpers\Html::a('<img src="/img/icons/lnurl-pay.svg" style="height: 25px" />LNURL-pay',['wallet/lnurl-pay','id'=>$wallet->external_hash],['class'=>'sidebar-item']);?>
        <?=\yii\helpers\Html::a('<img src="/img/icons/rpc-forwarder.svg" style="height: 25px" />Keysend',['wallet/keysend','id'=>$wallet->external_hash],['class'=>'sidebar-item']);?>
        <?=\yii\helpers\Html::a('<img src="/img/icons/keys-auth.svg" style="height: 25px" />Access Keys',['wallet/access-keys','id'=>$wallet->external_hash],['class'=>'sidebar-item']);?>
        <?=\yii\helpers\Html::a('<img src="/img/icons/events.svg" style="height: 25px" /> Transactions',['wallet/transactions','id'=>$wallet->external_hash],['class'=>'sidebar-item']);?>
        <?=\yii\helpers\Html::a('<img src="/img/icons/nodes.svg" style="height: 25px" />LN Node: '.@$wallet->lnNode->alias,['wallet/ln-node','id'=>$wallet->external_hash],['class'=>'sidebar-item']);?>

    </div>
    <div>
        <?=$content;?>
    </div>
</div>

 <? */ ?>
<h1><?=$wallet->user_label;?></h1>
<section id="model_6">
<?php
echo \yii\bootstrap4\Tabs::widget([
    'items' => [
        [
            'label' => 'Info',
            'content'=> $content,
            'url' => $this->context->action->id == 'view' ? NULL : ['wallet/view','id'=>$wallet->external_hash],
            'active' => $this->context->action->id == 'view'
        ],
        /*[
            'label' => 'Send',
            'content'=> $content,
            'url' => $this->context->action->id == 'send' ? NULL : ['wallet/send','id'=>$wallet->external_hash],
            'active' => $this->context->action->id == 'send'
        ],
        [
            'label' => 'Receive',
            'content'=> $content,
            'url' => $this->context->action->id == 'receive' ? NULL : ['wallet/receive','id'=>$wallet->external_hash],
            'active' => $this->context->action->id == 'receive'
        ],
        [
            'label' => 'Transfer',
            'content'=> $content,
            'url' => $this->context->action->id == 'transfer' ? NULL : ['wallet/transfer','id'=>$wallet->external_hash],
            'active' => $this->context->action->id == 'transfer'
        ],*/
        [
            'label' => 'Access Keys',
            'content' => $content,
            'url' => $this->context->action->id == 'access-keys' ? NULL : ['wallet/access-keys','id'=>$wallet->external_hash],
            'active'=> $this->context->action->id == 'access-keys'
        ],
        [
            'label' => 'Transactions',
            'content' => $content,
            'url' => $this->context->action->id == 'transactions' ? NULL : ['wallet/transactions','id'=>$wallet->external_hash],
            'active'=> $this->context->action->id == 'transactions'
        ],
    ],
]);
?></section>

