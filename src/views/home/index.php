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

?>

<section class="vh-100">
    <div class="container-fluid h-custom">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-md-9 col-lg-6 col-xl-5">
                <img src="/img/LN-Pay-Full-600.png"
                     class="img-fluid" alt="Sample image">
            </div>
            <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                <form>
                    <?php if (\LNPay::$app->user->isGuest) { ?>
                        <?=$this->render('signup',['model'=>new SignupForm()]);?>
                    <?php } else { ?>
                        You are logged in!
                    <?php } ?>


                </form>
            </div>
        </div>
    </div>
</section>