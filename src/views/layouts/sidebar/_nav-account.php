<?=$this->render('__base_sidebar'); ?>
<?php /* ?>
<div class="sidebar-layout">
    <div class="sidebar">
        <a class="sidebar-item" href="/settings/mfa"><img src="/img/icons/authenticator.svg" style="height: 25px" />Multi-Factor Auth</a>
        <a class="sidebar-item" href="/account/index"><img src="/img/icons/settings.svg" style="height: 25px" />Your Account</a>
        <a class="sidebar-item" href="/account/product"><img src="/img/icons/nodes.svg" style="height: 25px" />Your Products</a>
    </div>
    <div>
        <?=$content;?>
    </div>
</div>
<?php */ ?>

<section id="model_6">
<?php
echo \yii\bootstrap4\Tabs::widget([
    'items' => [
        [
            'label' => 'Account',
            'content'=> $content,
            'url' => $this->context->action->id == 'index' ? NULL : ['account/index'],
            'active' => $this->context->action->id == 'index'
        ],
        [
            'label' => 'Change Password',
            'content' => $content,
            'url' => $this->context->action->id == 'change-password' ? NULL : ['/account/change-password'],
            'active'=> $this->context->action->id == 'change-password'
        ],
        [
            'label' => 'Products',
            'content' => $content,
            'url' => $this->context->action->id == 'product' ? NULL : ['account/product'],
            'active'=> $this->context->action->id == 'product'
        ],
    ],
]);
?>

</section>

