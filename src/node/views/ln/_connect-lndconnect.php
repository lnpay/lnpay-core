<h3>LNDConnect by macaroon</h3>
<?php foreach ($node->lnNodeProfiles as $lnp) {
    echo $lnp->user_label;
    echo "<br/>";
    echo '<pre><code>'.$lnp->lndConnectString."</code></pre>";

    if (strlen($lnp->lndConnectString)<2953)
        echo '<img src="'.(new \chillerlan\QRCode\QRCode())->render($lnp->lndConnectString).'" alt="QR Code" />';

    echo "<br/>";
    echo "<br/>";
} ?>
