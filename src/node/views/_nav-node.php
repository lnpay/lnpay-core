<?php

use yii\helpers\Html;

?>
<?=$this->render('@app/views/layouts/sidebar/__base_sidebar'); ?>

<?php $node = \LNPay::$app->controller->nodeObject; ?>


<div class="sidebar-layout">
    <div class="sidebar">
        <a class="sidebar-item" href="/node/ln/index/<?=$node->id;?>"><img src="/img/icons/node-info.svg" style="height: 25px" />Node Info</a>
        <a class="sidebar-item" href="/node/ln/onchain/<?=$node->id;?>"><img src="/img/icons/chain.svg" style="height: 25px" />On-chain BTC</a>
        <a class="sidebar-item" href="/node/ln/networkfees/<?=$node->id;?>"><img src="/img/icons/fees.svg" style="height: 25px" />Lightning Network Fees</a>
        <?php /* ?><a class="sidebar-item" href="/node/ln/connect/<?=$node->id;?>"><img src="/img/icons/node-connect.svg" style="height: 25px" />Connect</a><?php */ ?>
        <hr>
        <a class="sidebar-item" href="/node/authprofile/index/<?=$node->id;?>"><img src="/img/icons/keys-auth.svg" style="height: 25px" />Macaroon Bakery</a>
        <a class="sidebar-item" href="/node/rpc/listeners/<?=$node->id;?>"><img src="/img/icons/rpc-listen.svg" style="height: 25px" />RPC Listeners</a>
        <a class="sidebar-item" href="/node/rpc/forwarder/<?=$node->id;?>"><img src="/img/icons/rpc-forwarder.svg" style="height: 25px" />RPC Event HTTP Forwarder</a>
        <hr>
        <hr>
        <?php echo Html::a('Remove Node', ['/node/ln/delete', 'id' => $node->id], [
            'class' => 'btn-danger pull-right sidebar-item',
            'style'=>'height:25px',
            'data' => [
                'confirm' => 'Are you sure you want to remove this node? This will remove all wallets and transactions associated with this node!! CANNOT BE UNDONE',
                'method' => 'post',
            ],
        ]); ?>
    </div>
    <div>
        <?=$content;?>
    </div>
</div>


