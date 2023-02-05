<?php
/* @var  \lnpay\wallet\models\Wallet $wallet */

$this->title = "Access Keys: ".$wallet->user_label;
$this->params['breadcrumbs'][] = ['label' => 'Wallets', 'url' => ['/wallet/dashboard']];
$this->params['breadcrumbs'][] = ['label' => $wallet->user_label, 'url' => ['/wallet/view','id'=>$wallet->external_hash]];
$this->params['breadcrumbs'][] = 'Loop In/Out';
?>

<?php $this->beginContent('@app/wallet/views/layouts/_nav-wallets.php',compact('wallet')); ?>
<h1>Loop Out</h1>
<div class="wallet-content-item">
    <div class="wallet-loop-container collapse">
        <div class="balance-area">
            <h4>Wallet Balance</h4>
            <h1><?=$wallet->availableBalance;?> <small>sats</small></h1>
        </div>

        <div class="row">
                <?php $loopOutWidget = \yii\widgets\ActiveForm::begin([
                    'options'=>[
                        'class'=>'ajaxFormLoader'
                    ],
                ]); ?>
                <?php //$form->errorSummary($model); ?>

                <?= $loopOutWidget->field($lnLoopOutForm, 'num_satoshis')->textInput(['placeholder'=>'e.g. 10000', 'rows'=>4]); ?>
                <?= $loopOutWidget->field($lnLoopOutForm, 'addr')->textInput(['placeholder'=>'e.g. bc1...']); ?>
                <?= $loopOutWidget->field($lnLoopOutForm, 'channel')->textInput(['placeholder'=>'e.g. 772939485837331841'])->hint('This will be left blank unless instructed otherwise'); ?>
                <?= \yii\helpers\Html::submitButton('Loop Out!', ['class' => 'styled-button-success','style'=>'white-space:unset;']) ?>
                <?php \yii\widgets\ActiveForm::end(); ?>
            </div>
    </div>
</div>

<?php $this->endContent(); ?>
