<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model lnpay\node\models\LnNode */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="container">
    <div class="ln-node-form col-md-6">

        <?php $form = ActiveForm::begin(); ?>

        <pre><code>$ lndconnect -j --readonly</code></pre>
        OR
        <pre><code>$ echo 'lndconnect://23.23.23.23:10013?cert='"`grep -v 'CERTIFICATE' ~/.lnd/tls.cert | tr -d '=' | tr '/+' '_-'`"'&macaroon='"`base64 ~/.lnd/data/chain/bitcoin/mainnet/readonly.macaroon | tr -d '=' | tr '/+' '_-'`" | tr -d '\n'</code></pre>
        <?= $form->field($model, 'lndconnect_readonly')->textarea(['maxlength' => true,'placeholder'=>'e.g. lndconnect://'])->label('Copy lndconnect string here') ?>


        <div class="form-group">
            <?= Html::submitButton('Verify Connection', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

        <br/><br/>

        <?php
        if ($readonlyMacaroonObject) {
            echo "Macaroon Permissions:<pre><code>";
            \yii\helpers\VarDumper::dump($readonlyMacaroonObject->permissions);
            echo "</code></pre>";
        }

        if ($nodeInfo) {
            echo "Node Info:<pre><code>";
            \yii\helpers\VarDumper::dump((array)$nodeInfo);
            echo "</code></pre>";
        }

        ?>

    </div>

    <div class="ln-node-form col-md-6">
        <h3>
            Details
        </h3>
        <p>
            <ul>
                <li>
                    READONLY macaroon to start
                </li>
                <li>
                    Both RPC and REST access is required.
                </li>
                <li>
                    You will only be able to receive money via a wallet or paywall
                </li>
                <li>
                    Since the money is in your node, the wallets really serve as a record keeping layer
                </li>
            </ul>
        </p>

    </div>

</div>
