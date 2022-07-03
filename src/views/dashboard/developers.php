<?php $this->beginContent('@app/views/layouts/sidebar/_nav-developers.php');

use yii\helpers\Html;

$this->title = 'Developers';
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="paywalls-header">
        <h2 style="margin-top: 0px;">Settings</h2>
    </div>

    <div class="wallet-content-item">
        <div class="container-fluid">
                <div class="balance-area">
                    <h3>Public API Key (to be used client side)</h3>
                    <pre><?=\LNPay::$app->user->identity->getFirstAccessKeyByRole(\lnpay\behaviors\UserAccessKeyBehavior::ROLE_PUBLIC_API_KEY);?></pre>
                    <h3>Secret API Key (to be used server side only!)</h3>
                    <pre><?=\LNPay::$app->user->identity->getFirstAccessKeyByRole(\lnpay\behaviors\UserAccessKeyBehavior::ROLE_SECRET_API_KEY);?></pre>
                    <br/>
                    <?= Html::a('API Documentation <i class="fa fa-external-link-alt"></i>', 'https://docs.lnpay.co', ['class' => 'btn btn-info','target'=>'_blank']) ?>
                    <br/>
                    <br/>
                   </div>
        </div>
    </div>




<?php $this->endContent();?>