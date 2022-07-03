<?php

use yii\helpers\Html;


$this->title = 'LN Nodes';
$this->params['breadcrumbs'][] = $this->title;



if ($details = \LNPay::$app->session->getFlash('new_node_details')) {
    \LNPay::$app->session->set('new_node_details_1',$details);
    \yii\bootstrap\Modal::begin([
        'id'=>'cred-modal',
        'header' => '<h2>'.$details['node_id'].' Credentials</h2>',
        'clientOptions' => ['backdrop' => 'static','tabindex'=>'-1']
    ]);

    echo 'You will NOT be able to access these again!';
    echo '<br/>';
    echo Html::a('<span class="btn btn-info">Download Credentials <i class="fa fa-arrow-alt-circle-down"></i></span>',['/node/dashboard/dl-cred'],['onClick'=>'$("#cred-modal").modal("hide");']);

    \yii\bootstrap\Modal::end();
    $this->registerJs('$("#cred-modal").modal("show");');
}

?>

<?php if ($nodes->count() > 0) { ?>
<div class="ln-node-index">
    <h1>Available Nodes</h1>

    <div class="row">
        <?php foreach ($nodes->all() as $node) { ?>
            <div class="col-sm-6 col-md-4">
                <div class="thumbnail text-center">
                    <h2><?=$node->getInfoValueByKey('alias');?></h2>
                    <div class="caption">
                        <p><strong>
                            <?php if ($node->user_id == \LNPay::$app->user->id) { ?>
                                <?=$node->getInfoValueByKey('version');?>
                            <?php } ?>
                        </strong></p>
                        <p><span class="badge"><strong><?=$node->network;?></strong></span></p>
                        <br/>
                        <p>
                            <?php if ($node->user_id == \LNPay::$app->user->id) { ?>
                                <?=Html::a('View Node <i class="fas fa-arrow-right"></i>',['/node/ln/index','id'=>$node->id],['class'=>'btn btn-primary']);?>
                            <?php } else { ?>
                                <span class="badge"><strong><?=$node->org->display_name;?></strong></span>
                            <?php } ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
<?php } ?>

</div>