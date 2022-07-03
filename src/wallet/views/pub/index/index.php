
<h1>Send / Receive / Transfer</h1>
<div class="wallet-content-item">
    <div class="wallet-balance-container">
        <div class="balance-area">
            <h4>Balance</h4>
            <h1><?=$wallet->availableBalance;?> <small>sats</small></h1>
        </div>

        <div class="widthdraw-area">
            <button class="styled-button-success withdraw-collapse-button collapsed" type="button" data-toggle="collapse" data-target="#withdraw-details" aria-expanded="false" aria-controls="withdraw-details">
                Send
            </button>
        </div>
        <div class="balance-area"></div>
        <div class="deposit-area">
            <button class="styled-button-success deposit-collapse-button collapsed" type="button" data-toggle="collapse" data-target="#deposit-details" aria-expanded="false" aria-controls="deposit-details">
                Receive
            </button>
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
            <?php $wForm = \yii\widgets\ActiveForm::begin([
                'enableAjaxValidation'=>true,
                'options'=>[
                    'class'=>'ajaxFormLoader'
                ],
                'validationUrl'=>'/wallet/wallet/validate-withdrawal?id='.$wallet->publicId
            ]); ?>
            <?php //$form->errorSummary($model); ?>

            <?= $wForm->field($wModel, 'payment_request')->textArea(['placeholder'=>'e.g. lnbc10u1pwcxqfkpp5e9nu85e6fypp89ql0hnz7yj784jatyytugewmpwe9yqhla7zvg3sdqdfdshjsn92djk2cqzpgjuk3ljzzrqhwsfr36h0nnyzy3gx3sna3fdnj9pkcqakjnkly0cdhk0lagf763mtegeld78qdpwf7t52mvgxl3f8neuty8y0pvvvffjcp96x09n', 'rows'=>4]); ?>
            <?= \yii\helpers\Html::submitButton('Send ⚡', ['class' => 'styled-button-success','style'=>'white-space:unset;']) ?>
            <?php \yii\widgets\ActiveForm::end(); ?>
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
            <?php $dForm = \yii\widgets\ActiveForm::begin([
                'options'=>[
                    'class'=>'ajaxFormLoader'
                ],
            ]); ?>
            <?php //$form->errorSummary($model); ?>

            <?= $dForm->field($dModel, 'num_satoshis')->textInput(['placeholder'=>'e.g. 20']); ?>
            <?= $dForm->field($dModel, 'memo')->textInput(['placeholder'=>'e.g. For Deposit']); ?>

            <?php
            $key = $wallet->getFirstAccessKeyByRole(\lnpay\behaviors\UserAccessKeyBehavior::ROLE_WALLET_INVOICE);
            $userKey = $wallet->user->getFirstAccessKeyByRole(\lnpay\behaviors\UserAccessKeyBehavior::ROLE_PUBLIC_API_KEY);
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

            <?= \yii\helpers\Html::a('Generate Invoice ⚡', '#',['class' => 'styled-button-success','style'=>'white-space:unset;','onClick'=>'createInvoice();return false;']) ?>
            <?php \yii\widgets\ActiveForm::end(); ?>
        </div>
        <div class="col-md-6" id="showInvoice" style="overflow-wrap: break-word;">

        </div>
    </div>
</div>