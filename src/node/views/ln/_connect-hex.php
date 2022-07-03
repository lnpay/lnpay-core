<h3>TLS Cert</h3>
<pre><?=$node->tls_cert;?></pre>


<h3>Macaroons</h3>
<?php foreach ($node->lnNodeProfiles as $lnp) {
    echo $lnp->user_label;
    echo "<br/>";
    echo '<pre>'.$lnp->macaroonObject->hex."</pre>";
} ?>
