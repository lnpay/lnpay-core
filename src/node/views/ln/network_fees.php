<?php

$this->title = 'Lightning Network Fees';
$feeTargetWallet = \LNPay::$app->user->identity->getJsonData(\lnpay\models\User::DATA_FEE_TARGET_WALLET);
$feeWallet = $node->feeWallet;
?>

    <h1>Lightning Network Fees</h1>
    <p>
        <?php switch ($feeTargetWallet) {
            case \lnpay\models\User::DATA_FEE_TARGET_WALLET_CONTAINED:
                echo 'Lightning Network routing fees are deducted from the balance of a wallet when you send.';
                break;
                default:
                    echo 'Lightning Network routing fees are collected in a dedicated "Fee Wallet" and will run a negative balance.';

        } ?>

    </p>

    <div class="well">
        <p>
            See below for fee settings.
        </p>
        <p>
            <?php /* ?>
            Current INBOUND (deposits) fee rate: <strong><?=\LNPay::$app->user->identity->getServiceFeeRate(\lnpay\wallet\models\WalletTransactionType::LN_DEPOSIT)*100;?>%</strong>
            <br/>
            Current OUTBOUND (withdrawals) fee rate: <strong><?=\LNPay::$app->user->identity->getServiceFeeRate(\lnpay\wallet\models\WalletTransactionType::LN_WITHDRAWAL)*100;?>%</strong>
            <br/>
            Current TRANSFER (transfers) fee rate: <strong>0%</strong>
            <br/>
            <?php */ ?>
            Lightning Network routing fees: <strong><?=$feeTargetWallet;?> wallet</strong>
            <br/>
            Max routing fee allowed: <strong><?=(\LNPay::$app->user->identity->getJsonData(\lnpay\models\User::DATA_MAX_NETWORK_FEE_PERCENT)?:5);?>%</strong>
        </p>

        <div>
            <p>
                <?=\yii\helpers\Html::a('Go to Fee Wallet > ',['/wallet/view','id'=>$feeWallet->external_hash],['class'=>'btn btn-primary']); ?>
            </p>
        </div>
    </div>
