<?php
/* @var  \lnpay\wallet\models\Wallet $wallet */
/* @var  \lnpay\wallet\models\WalletNodeChangeForm $walletNodeChangeForm */
$this->title = "LN Node: ".$wallet->user_label;
$this->params['breadcrumbs'][] = ['label' => 'Wallets', 'url' => ['/wallet/dashboard']];
$this->params['breadcrumbs'][] = ['label' => $wallet->user_label, 'url' => ['/wallet/view','id'=>$wallet->external_hash]];
$this->params['breadcrumbs'][] = 'Lightning Node';

$lnNode = $wallet->lnNode;
$user = \LNPay::$app->user->identity;
?>

<?php $this->beginContent('@app/wallet/views/layouts/_nav-wallets.php',compact('wallet')); ?>
<div class="jumbotron well">
    <h1><?=$lnNode->alias;?> (<?=$lnNode->org->display_name;?>)</h1>
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
<?php

if ($user->lnNode) {

?>
<div class="col-md-6 well">
    <h2>Change Node Form</h2>
    <div class="row">
        <?php $form = \yii\widgets\ActiveForm::begin([
            'options'=>[],
        ]); ?>
        <?php // $form->errorSummary($walletNodeChangeForm); ?>

        <?php
            $q = $user->getLnNodeQuery()->all();
            echo $form->field($walletNodeChangeForm, 'target_ln_node_id')->dropDownList(\yii\helpers\ArrayHelper::map($q,'id','alias'),[]);
        ?>
        <?= $form->field($walletNodeChangeForm, 'wallet_id')->hiddenInput(['value'=>$wallet->external_hash])->label(false); ?>
        <?= $form->field($walletNodeChangeForm, 'transfer_balance')->checkbox()->hint('Transfer the sat balance ('.$wallet->balance.' sat) to the new node via keysend'); ?>
        <?= \yii\helpers\Html::submitButton('Change Node', ['class' => 'styled-button-success','style'=>'white-space:unset;']) ?>
        <?php \yii\widgets\ActiveForm::end(); ?>
    </div>
</div>
<?php } ?>


<?php $this->endContent(); ?>
