<?=$this->render('@app/views/layouts/sidebar/__base_sidebar'); ?>

<div class="sidebar-layout">
    <div class="sidebar">
        <a class="sidebar-item" href="/node/dashboard/index"><img src="/img/icons/nodes.svg" style="height: 25px" />Lightning Nodes</a>
        <hr>
        <a class="sidebar-item" href="/node/dashboard/add"><img src="/img/icons/node-add.svg" style="height: 25px" />Add LND Node</a>
    </div>
    <div>
        <?=$content;?>
    </div>
</div>


