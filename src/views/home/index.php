<?php /** @noinspection ALL */

/* @var $this yii\web\View */

use lnpay\models\SignupForm;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
use Dusterio\LinkPreview\Client;
use \lnpay\models\link\Rule;
use \yii\helpers\HtmlPurifier;

$this->title = \LNPay::$app->name;

$this->registerJs('
$("#withdraw-loader").hide();
$(\'#withdrawForm\').on(\'ajaxBeforeSend\', function (event, jqXHR, settings) {
    // Activate waiting label
    $("#withdraw-loader").show();
}).on(\'ajaxComplete\', function (event, jqXHR, textStatus) {
    // Deactivate waiting label
    $("#withdraw-loader").hide();
});
');



?>
<div class="site-index">
    <div class="body-content">
        <div class="container">
            <div class="front-page-content">
                <div class="main-message">
                    <h1>LNPay</h1>
                    <h4>
                        <ul>
                            <li>Enterprise Lighting Network Toolkit</li>
                        </ul>

                    </h4>
                </div>
                <div class="main-cta-box">
                    <?php if (\LNPay::$app->user->isGuest) { ?>
                        <?=$this->render('signup',['model'=>new SignupForm()]);?>
                    <?php } else { ?>
                        You are logged in!
                    <?php } ?>
                </div>
            </div>
        </div>