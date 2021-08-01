<?=$this->render('__base_sidebar'); ?>

<div class="sidebar-layout">
    <div class="sidebar">
        <?php /* ?><a class="sidebar-item" href="/settings/mfa"><img src="/img/icons/authenticator.svg" style="height: 25px" />Multi-Factor Auth</a><?php */?>
        <a class="sidebar-item" href="/account/index"><img src="/img/icons/settings.svg" style="height: 25px" />Your Account</a>
        <a class="sidebar-item" href="/account/product"><img src="/img/icons/nodes.svg" style="height: 25px" />Your Products</a>
    </div>
    <div>
        <?=$content;?>
    </div>
</div>


