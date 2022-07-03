
<?php /*
    $hose = $model->addHose(\lnpay\models\DistroMethod::findOne(\lnpay\models\DistroMethod::RAW_LNURL));
?>
HOSE TAG: <?=$hose->faucet_tag;?>

 <?php */?>
<pre><code><?=@file_get_contents($model->baseLink->getUrl([],\lnpay\models\DistroMethod::NAME_RAW_LNURL));?></code></pre>