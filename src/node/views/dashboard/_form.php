<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model lnpay\node\models\LnNode */
/* @var $form yii\widgets\ActiveForm */

$bakeMac = new \lnpay\node\components\LnMacaroonObject();
$bakeMac->permissions = $model->defaultMacaroonPerms;

?>

<div class="container">
    <div class="ln-node-form col-md-6">
        <?php echo Html::errorSummary($model); ?>
        <?php $form = ActiveForm::begin(); ?>

        <h2>Network</h2>
        <?= $form->field($model, 'node_host')->textInput(['maxlength' => true,'placeholder'=>'e.g. 125.125.125.125'])->label('Node IP / HOST'); ?>
        <?= $form->field($model, 'node_grpc_port')->textInput(['maxlength' => true,'placeholder'=>'e.g. 10009'])->label('GRPC Port'); ?>
        <?= $form->field($model, 'node_rest_port')->textInput(['maxlength' => true,'placeholder'=>'e.g. 8080'])->label('REST Port'); ?>

        <?= $form->field($model, 'node_macaroon')->textarea(['maxlength' => true,'placeholder'=>'e.g. 0201036c6e6402a80....'])->label('Baked Macaroon HEX'); ?>

        <h2>TLS</h2>
        Please see: <a href="https://docs.zaphq.io/docs-desktop-lnd-configure">https://docs.zaphq.io/docs-desktop-lnd-configure</a> <br/>
        <?= $form->field($model, 'node_tls_cert')
                ->textarea(['maxlength' => true,'placeholder'=>'e.g. -----BEGIN CERTIFICATE-----'])
                ->label('TLS Cert raw or HEX')
                ->hint('The HOST above MUST match your externalip OR tlsextraip in lnd.conf!!'); ?>
        <?php if (\LNPay::$app->session->getFlash('invalid_tls')) { ?>
            <p class="alert alert-danger">
                We are practicing good node hygiene here. You probably need to add the following to lnd.conf:
                <br/><br/>
                tlsextraip=<?=$model->node_host;?>
                <br/><br/>
                Delete tls.cert and tls.key and restart LND
            </p>
        <?php } ?>
        <div class="form-group">
            <div id="node-add-warning" class="alert alert-info">
                <?php
                echo $form->field($model, 'is_custodian')->checkbox()->hint('Give other members access to this node?'); ?>
            </div>
        </div>
        <div class="form-group">
            <div id="node-add-warning" class="alert alert-info">
                <?php
                $model->readyToAdd = false;
                echo $form->field($model, 'readyToAdd')->checkbox(); ?>
            </div>
            <?php echo Html::submitButton('Check Connection', ['id'=>'add-node-submit-button','class' => 'btn btn-info']); ?>
        </div>

        <?php ActiveForm::end(); ?>

        <br/><br/>

    </div>

    <div class="ln-node-form col-md-6">


        <?php

        if ($nodeInfo) {
            echo "Node Info:<pre><code>";
            if ($n = $nodeInfo) {
                echo json_encode($n,JSON_PRETTY_PRINT);
            }
            else
                echo $nodeInfo;
            echo "</code></pre>";
        }
        /*
        if ($submittedMacaroonObject) {
            echo "Macaroon Permissions:<pre><code>";
            \yii\helpers\VarDumper::dump($submittedMacaroonObject->permissions);
            echo "</code></pre>";
        }*/
        ?>
    </div>

</div>

<?php $this->registerJs("
     $('#nodeaddform-readytoadd').change(function() {
        if(this.checked) {
            $('#add-node-submit-button').text('Add Node!');
            $('#add-node-submit-button').attr('class','btn btn-success');
        } else {
            $('#add-node-submit-button').text('Check Connection');
            $('#add-node-submit-button').attr('class','btn btn-info');
        }
              
    });
"); ?>