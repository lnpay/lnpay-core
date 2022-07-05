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
    <?php
        NavBar::begin([
            'brandLabel' => '⚡'.\LNPay::$app->name,
            'brandUrl' => (\LNPay::$app->user->isGuest?\LNPay::$app->homeUrl:'/dashboard/home'),
            'options' => [
                'class' => '' . $navclass,
            ],
        ]);
    $menuItemsLeft = [
        /*[
            'label' => '<img src="/img/icons/home.svg" style="width:15px" /> Legacy LAPPs',
            'url' => 'https://legacy.lnpay.co/',
            'linkOptions'=>['target'=>'_blank'],
            'encode'=>false,
        ],*/
        [
            'label' => '<img src="/img/icons/wallet.svg" style="width:15px" /> Wallets',
            'url' => (\LNPay::$app->user->isGuest?'#':['/wallet/wallet/dashboard']),
            'active'=>stripos(\LNPay::$app->request->pathInfo,'wallet')!==FALSE,
            'encode'=>false,
        ],
        [
            'label' => '<img src="/img/icons/nodes.svg" style="width:15px" /> LN Nodes',
            'url' => (\LNPay::$app->user->isGuest?'#':['/node/dashboard/index']),
            'active'=>\lnpay\components\HelperComponent::str_contains(\LNPay::$app->request->pathInfo,['node/']),
            'encode'=>false,
        ],
        [
            'label' => '<img src="/img/icons/flowchart.svg" style="width:15px" /> Developers',
            'url' => (\LNPay::$app->user->isGuest?'#':['/developers/dashboard']),
            'active'=>\lnpay\components\HelperComponent::str_contains(\LNPay::$app->request->pathInfo,['developers','webhook']),
            'encode'=>false,
        ],
    ];
    if (\LNPay::$app->user->isGuest) {
        $menuItemsRight[] = ['label' => 'Login', 'url' => ['/home/login']];
        $menuItemsRight[] = ['label' => 'Get Started', 'class' => 'btn-outline-success', 'url' => ['/home/signup']];
    } else {
        $menuItemsRight = [

            ['label' => \LNPay::$app->user->identity->email, 'url' => '/account/index'],
            '<li class="nav-item dropdown">'
            . '<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 12px">'
            . '<img src="/img/icons/settings.svg" style="height: 25px;"/>'
            . '</a>'
            . '<div class="dropdown-menu" aria-labelledby="navbarDropdown">'
            .'<a class="dropdown-item btn btn-link" href="/org/home/view">'.$user->org->display_name.'</a>'
            .'<a class="dropdown-item btn btn-link" href="/account/index">Account Info</a>'
                . '<a class="dropdown-item">'
                . Html::beginForm(['/home/logout'], 'post')
                . Html::submitButton(
                    'Logout',
                    ['class' => 'btn btn-link logout']
                )
                . Html::endForm() . '</a>'
            . '</div>'
          . '</li>',
        ];
    }

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-left'],
        'items' => $menuItemsLeft,
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItemsRight,
    ]);
    NavBar::end();
    ?>

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

            <?php
                if (stripos(\LNPay::$app->controller->module->id,"basic") === false && \LNPay::$app->controller->module->sidebarView)
                    $this->beginContent(\LNPay::$app->controller->module->sidebarView);
                ?>
                <?= $content ?>
            <?php if (stripos(\LNPay::$app->controller->module->id,"basic") === false && \LNPay::$app->controller->module->sidebarView)
                    $this->endContent(); ?>
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
