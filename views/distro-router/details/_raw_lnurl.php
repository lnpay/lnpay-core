
<?php /*
    $hose = $model->addHose(\app\models\DistroMethod::findOne(\app\models\DistroMethod::RAW_LNURL));
?>
HOSE TAG: <?=$hose->faucet_tag;?>

 <?php */?>
<pre><code><?=@file_get_contents($model->baseLink->getUrl([],\app\models\DistroMethod::NAME_RAW_LNURL));?></code></pre>