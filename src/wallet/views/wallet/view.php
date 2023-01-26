<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = "Wallet: ".$wallet->user_label;
$this->params['breadcrumbs'][] = ['label' => 'Wallets', 'url' => ['/wallet/wallet/dashboard']];
$this->params['breadcrumbs'][] = ['label' => $wallet->user_label, 'url' => ['/wallet/wallet/view','id'=>$wallet->external_hash]];
$this->params['breadcrumbs'][] = 'View';
?>

<?php $this->beginContent('@app/wallet/views/layouts/_nav-wallets.php',compact('wallet')); ?>

<div class="card p-5">
    <div class="row">
        <div class="col-md-3">
            <div class="balance-area">
                <h4>Balance</h4>
                <h1><?=$wallet->availableBalance;?> <small>sats</small></h1>
            </div>

                <div class="widthdraw-area">
                    <button class="btn btn-success withdraw-collapse-button collapsed" type="button" data-toggle="collapse" data-target="#withdraw-details" aria-expanded="false" aria-controls="withdraw-details">
                        Send
                    </button>
                </div>
            <div class="balance-area"></div>
            <div class="deposit-area">
                <button class="btn btn-success deposit-collapse-button collapsed" type="button" data-toggle="collapse" data-target="#deposit-details" aria-expanded="false" aria-controls="deposit-details">
                    Receive
                </button>
            </div>
            <div class="balance-area"></div>
            <div class="transfer-area">
                <button class="btn btn-success transfer-collapse-button collapsed" type="button" data-toggle="collapse" data-target="#transfer-details" aria-expanded="false" aria-controls="transfer-details">
                    Transfer
                </button>
            </div>
        </div>
        <div class="col-md-6">
            <div class="collapse" id="transfer-details" style="margin-top: 25px;">
                <button type="button" class="close close-transfer" data-toggle="collapse" data-target="#transfer-details" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h2>Transfer Details</h2>


                <div class="wallet-content-item row" style="padding: 25px; margin-bottom: 12px; box-shadow: 0 1px 3px hsla(0, 0%, 0%, .2);">
                    <div class="col-md-6">
                        <?php
                        $tModel->source_wallet_id = $wallet->publicId;
                        $tForm = ActiveForm::begin([
                            'options'=>[
                                'class'=>'formSpinnerLoader'
                            ]
                        ]); ?>
                        <?php //$form->errorSummary($model); ?>
                        <?= $tForm->field($tModel, 'source_wallet_id')->hiddenInput()->label(false); ?>
                        <?= $tForm->field($tModel, 'num_satoshis')->textInput(['placeholder'=>'e.g. 20'])->hint('Funds available in this wallet: '.$wallet->balance); ?>
                        <?= $tForm->field($tModel, 'memo')->textInput(['placeholder'=>'e.g. Stashing funds']); ?>
                        <?php
                        $wallets = \yii\helpers\ArrayHelper::map($availableWalletsForTransferQuery->asArray()->all(),'external_hash',function($row) { return $row['user_label'].' (Balance: '.$row['balance'].')'; });
                        echo $tForm->field($tModel, 'dest_wallet_id')->dropDownList($wallets,['prompt'=>'Choose Destination Wallet']); ?>
                        <?= Html::submitButton('Transfer', ['class' => 'btn btn-success','style'=>'white-space:unset;']) ?>
                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
            <div class="collapse" id="withdraw-details" style="margin-top: 25px;">
                <button type="button" class="close close-withdraw" data-toggle="collapse" data-target="#withdraw-details" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h2>Send Details</h2>


                <div class="wallet-content-item row" style="padding: 25px; margin-bottom: 12px; box-shadow: 0 1px 3px hsla(0, 0%, 0%, .2);">
                    <div class="col-md-6">
                        <?php $wForm = ActiveForm::begin([
                            'enableAjaxValidation'=>true,
                            'options'=>[
                                'class'=>'ajaxFormLoader'
                            ],
                            'validationUrl'=>'/wallet/wallet/validate-withdrawal?id='.$wallet->publicId
                        ]); ?>
                        <?php //$form->errorSummary($model); ?>

                        <?= $wForm->field($wModel, 'payment_request')->textArea(['placeholder'=>'e.g. lnbc10u1pwcxqfkpp5e9nu85e6fypp89ql0hnz7yj784jatyytugewmpwe9yqhla7zvg3sdqdfdshjsn92djk2cqzpgjuk3ljzzrqhwsfr36h0nnyzy3gx3sna3fdnj9pkcqakjnkly0cdhk0lagf763mtegeld78qdpwf7t52mvgxl3f8neuty8y0pvvvffjcp96x09n', 'rows'=>4]); ?>
                        <?= Html::submitButton('Send ⚡', ['class' => 'btn btn-success','style'=>'white-space:unset;']) ?>
                        <?php ActiveForm::end(); ?>
                    </div>
                    <div class="col-md-6">

                        <?php $lnurl = $wallet->getLnurlWithdrawLinkEncoded(null,['ott'=>'ui-w']) ?>
                        <a href="lightning:<?=$lnurl;?>">
                            <?='<img src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=' .$lnurl. '" />';?>
                        </a>
                        <p><?=\LNPay::$app->name;?> supports LNURL - Scan or click with a LNURL compatible wallet.</p>

                    </div>
                </div>
            </div>
            <div class="collapse" id="deposit-details" style="margin-top: 25px;">
                <button type="button" class="close close-deposit" data-toggle="collapse" data-target="#deposit-details" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h2>Receive Details</h2>
                <div class="wallet-content-item row" style="padding: 25px; margin-bottom: 12px; box-shadow: 0 1px 3px hsla(0, 0%, 0%, .2);">
                    <div class="col-md-6">
                        <?php $dForm = ActiveForm::begin([
                            'options'=>[
                                'class'=>'ajaxFormLoader'
                            ],
                        ]); ?>
                        <?php //$form->errorSummary($model); ?>

                        <?= $dForm->field($dModel, 'num_satoshis')->textInput(['placeholder'=>'e.g. 20']); ?>
                        <?= $dForm->field($dModel, 'memo')->textInput(['placeholder'=>'e.g. For Deposit']); ?>

                        <?php
                        $key = $wallet->getFirstAccessKeyByRole(\lnpay\behaviors\UserAccessKeyBehavior::ROLE_WALLET_INVOICE);
                        $userKey = \LNPay::$app->user->identity->getFirstAccessKeyByRole(\lnpay\behaviors\UserAccessKeyBehavior::ROLE_PUBLIC_API_KEY);
                        $this->registerJs("
               function createInvoice() {
                   $.ajax({
                    url: '/v1/wallet/$key/invoice',
                    headers: {
                        'X-Api-Key': '".$userKey."'
                    },
                    type: 'post',
                    data: {num_satoshis:$('#lnwalletdepositform-num_satoshis').val()  , memo:$('#lnwalletdepositform-memo').val()},
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(JSON.parse(xhr.responseText).message);
                    },
                    success: function (data) {
                      $('#showInvoice').html('<img src=\"https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl='+data.payment_request+'\" /><br/>'+data.payment_request);
                      var refreshInterval = setInterval(function(data){
                        var req = $.ajax({
                            type:\"get\",
                            url:\"/v1/lntx/\"+data.id+\"?access-token=".$userKey."\",
                        });
                        req.done(function(data){
                            //console.log(data);
            
                            if (data.settled) {
                                clearInterval(refreshInterval);
                                alert('Deposit received!');
                                window.location.reload();
                            }
                        });
                    },5000,data);
                    }});
                   
               }
                   ",\yii\web\View::POS_END); ?>

                        <?= Html::a('Generate Invoice ⚡', '#',['class' => 'btn btn-success','style'=>'white-space:unset;','onClick'=>'createInvoice();return false;']) ?>
                        <?php ActiveForm::end(); ?>
                    </div>
                    <div class="col-md-6" id="showInvoice" style="overflow-wrap: break-word;">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
                <?php if ($wallet->isFeeWallet)
                        echo \yii\bootstrap4\Alert::widget([
                            'body' => 'Lightning Network routing fees incurred from using this node. You will see a negative balance.',
                            'options' => [
                                'id' => 'id-keysend-yay',
                                'class' => 'alert-info',
                            ]
                        ]);
                else if ($wallet->isKeysendWallet) {
                        echo \yii\bootstrap4\Alert::widget([
                            'body' => 'Inbound keysend payments to this node that do not have a mapping to a wallet are collected here',
                            'options' => [
                                'id' => 'id-keysend-yay',
                                'class' => 'alert-info',
                            ]
                        ]);
                }
                ?>


<br/><br/>

<?php $this->endContent();?>