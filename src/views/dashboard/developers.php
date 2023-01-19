<?php
use yii\helpers\Html;

$this->title = 'Developers';
$this->params['breadcrumbs'][] = $this->title;
?>

    <div class="card card-body bg-light">
        <div class="container-fluid">
                <h3>Public API Key (to be used client side)</h3>
                <pre><?=\LNPay::$app->user->identity->getFirstAccessKeyByRole(\lnpay\behaviors\UserAccessKeyBehavior::ROLE_PUBLIC_API_KEY);?></pre>
                <h3>Secret API Key (to be used server side only!)</h3>
                <pre><?=\LNPay::$app->user->identity->getFirstAccessKeyByRole(\lnpay\behaviors\UserAccessKeyBehavior::ROLE_SECRET_API_KEY);?></pre>
        </div>
    </div>
