<?=$this->render('@app/views/layouts/sidebar/__base_sidebar'); ?>

<div class="sidebar-layout">
    <div class="sidebar">
        <?php /* ?><a class="sidebar-item" href="/settings/mfa"><img src="/img/icons/authenticator.svg" style="height: 25px" />Multi-Factor Auth</a><?php */?>
        <a class="sidebar-item" href="/org/home/view"><img src="/img/icons/settings.svg" style="height: 25px" />Organization Details</a>
        <?php if (\LNPay::$app->user->identity->org_user_type_id == \lnpay\org\models\OrgUserType::TYPE_OWNER) { ?>
            <a class="sidebar-item" href="/org/home/members"><img src="/img/icons/people.svg" style="height: 25px" />Members</a>
        <?php } ?>
    </div>
    <div>
        <?=$content;?>
    </div>
</div>


