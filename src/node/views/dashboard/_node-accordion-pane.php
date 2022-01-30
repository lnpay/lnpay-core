<h2>Macaroon Hex</h2>
<div class="well">
    <pre><code><?=$lnNodeProfile->macaroonObject->hex;?></code></pre>
</div>

<h2>Permissions</h2>
<div class="well">
    <pre><code><?=\yii\helpers\VarDumper::export($lnNodeProfile->macaroonObject->permissions);?></code></pre>
</div>