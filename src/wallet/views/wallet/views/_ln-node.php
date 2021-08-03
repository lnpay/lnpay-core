<?php
/* @var  \lnpay\wallet\models\Wallet $wallet */
$this->title = "LN Node: ".$wallet->user_label;
$this->params['breadcrumbs'][] = ['label' => 'Wallets', 'url' => ['/wallet/dashboard']];
$this->params['breadcrumbs'][] = ['label' => $wallet->user_label, 'url' => ['/wallet/view','id'=>$wallet->external_hash]];
$this->params['breadcrumbs'][] = 'Lightning Node';

$lnNode = $wallet->lnNode;
$isCustodial = $wallet->ln_node_id == \lnpay\node\models\LnNode::getLnpayNodeQuery()->one()->id;
?>

<?php $this->beginContent('@app/views/layouts/sidebar/_nav-wallets.php',compact('wallet')); ?>
<div class="jumbotron well">
    <h1><?=$lnNode->alias;?></h1>
    <p>
    <ul class="list-group">
        <li class="list-group-item"><a target="_blank" href="https://1ml.com/node/<?=$lnNode->default_pubkey;?>"><?=$lnNode->default_pubkey;?></a></li>
    </ul>
    <?php if ($lnNode->user_id == \LNPay::$app->user->id) { ?>
        <p>
            <a href="/node/ln/index/<?=$lnNode->id;?>" class="btn btn-primary">View Node <i class="glyphicon glyphicon-arrow-right"></i></a>
        </p>
    <?php } ?>
    </p>
    <p> Lightning Network transactions for this wallet are handled via this node.</p>
</div>


<?php $this->endContent(); ?>
