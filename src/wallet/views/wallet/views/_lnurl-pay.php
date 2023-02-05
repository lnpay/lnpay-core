<?php
$this->title = "LNURL Pay: ".$wallet->user_label;
$this->params['breadcrumbs'][] = ['label' => 'Wallets', 'url' => ['/wallet/dashboard']];
$this->params['breadcrumbs'][] = ['label' => $wallet->user_label, 'url' => ['/wallet/view','id'=>$wallet->external_hash]];
$this->params['breadcrumbs'][] = 'LNURL Pay';
?>

<?php $this->beginContent('@app/wallet/views/layouts/_nav-wallets.php',compact('wallet')); ?>

<div class="jumbotron well">
    <h2>Scan to send</h2>
    <p>
        <img src="<?=(new \chillerlan\QRCode\QRCode())->render($wallet->defaultWalletLnurlpay->lnurl_encoded);?>">
    </p>
    <p>
        <pre><?php echo $wallet->defaultWalletLnurlpay->lnurl_encoded;?></pre>
        <?php //echo $wallet->defaultWalletLnurlpay->lnurl_decoded;?>
    </p>
</div>

<?php $this->endContent(); ?>
