<?php

use lnpay\models\User;$this->title = 'Your Products';
$this->beginContent('@app/views/layouts/sidebar/_nav-account.php');
$user = \LNPay::$app->user->identity;
$usage = $user->getWalletAPIUsageByPeriod(strtotime('-30 days'),time());

?>

    <h1>Products</h1>

    <div data-example-id="table-within-panel">
        <div class="panel panel-default">
            <div class="panel-heading"><h2>Usage</h2></div>
            <table class="table">
                <thead>
                <tr>
                    <th>Limits</th>
                    <th>Usage (past 30 days)</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        Invoice Limit: <?=($user->getJsonData(User::DATA_MAX_DEPOSIT)?:'unlimited').' sat';?><br/>
                        Send Limit: <?=($user->getJsonData(User::DATA_MAX_WITHDRAWAL)?:'unlimited').' sat';?>
                    </td>
                    <td><?=$usage['ln_inbound_volume'];?> Sats Received<br/><?=$usage['ln_outbound_volume'];?> Sats Sent</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>




<?php
$this->endContent();

?>