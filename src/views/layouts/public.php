<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use lnpay\assets\PaywallAsset;
use lnpay\widgets\Alert;

PaywallAsset::register($this);
$user = \LNPay::$app->user->identity;

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= \LNPay::$app->language ?>">
<head>
    <meta charset="<?= \LNPay::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?=$this->render('_header');?>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<?php
$controller = \LNPay::$app->controller;
$default_controller = \LNPay::$app->defaultRoute;
$externalActions = ['paywalls','index','feed'];
$isHome = (($controller->id === $default_controller) && (in_array($controller->action->id,$externalActions))) ? true : false;

$navclass = !$isHome ? 'navbar navbar-default' : ''
?>

<div class="wrap">
    <?php if($isHome): ?>
        <div class="crazy-shit-container">
            <div class="crazy-shit"></div>
        </div>
    <?php else: ?>

    <?php endif; ?>
    <div class="container">
        <?= Alert::widget() ?>
        <?= Breadcrumbs::widget([
            'homeLink' => [
                'label'=>'Home',
                'url'=>'/dashboard/home'
            ],
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],

        ]) ?>

        <?= $content ?>
    </div>
</div>
</div>

<!-- <style>
    div.required label.control-label:after {
        content: " *";
        color: red;
    }
</style> -->
<?=$this->render('_footer');?>

<?php $this->endBody() ?>
</body>
<?php
$js = <<<SCRIPT
/* To initialize BS3 tooltips set this below */
$(function () { 
   $('body').tooltip({
    selector: '[data-toggle="tooltip"]',
        html:true
    });
});
SCRIPT;
// Register tooltip/popover initialization javascript
$this->registerJs ( $js );
?>
</html>
<?php $this->endPage() ?>
