<?=$this->render('__base_sidebar'); ?>

<div class="sidebar-layout">
    <div class="sidebar">
        <a class="sidebar-item" href="/developers/dashboard"><img src="/img/icons/home.svg" style="height: 25px" />Dashboard</a>
        <a class="sidebar-item" href="/developers/webhook"><img src="/img/icons/webhook.svg" style="height: 25px" />Webhooks</a>
        <a class="sidebar-item" href="/developers/domain"><img src="/img/icons/domain.svg" style="height: 25px" />Domains</a>
        <a class="sidebar-item" href="/developers/api-log"><img src="/img/icons/log.svg" style="height: 25px" />API Logs &nbsp;</a>
        <a class="sidebar-item" href="/developers/events"><img src="/img/icons/events.svg" style="height: 25px" />Events &nbsp;</a>
    </div>
    <div>
        <?=$content;?>
    </div>
</div>


