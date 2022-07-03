<?php
use yii\widgets\Pjax;

Pjax::begin();
?>
<div class="col-sm-6 col-md-6">
    <div class="thumbnail text-center">
        <div class="caption">
            <h4><?=$model['ln_node_id'];?></h4>
            <h3><?=$model['method'];?></h3>
            <?php
            $info = $model->supervisorProcessInfo;
            if ($info['statename']=='RUNNING') { ?>
                <i class="fas fa-check fa-10x" style="color:green;"></i>
            <?php } else { ?>
                <i class="fas fa-times fa-10x" style="color:red;"></i>
            <?php } ?>
            <br/>
            <span class="badge"><?=$info['statename'];?></span>
            <br/>
            <br/>
            <?php
                switch ($info['statename']) {
                    case 'RUNNING':
                        //STOP
                        echo \yii\helpers\Html::a('Stop Listener',['/node/rpc/control-listener','action'=>'stop','nl_id'=>$model->id],['class'=>'btn btn-danger']);
                        break;
                    case 'STOPPED':
                    case 'EXITED':
                    case 'FATAL':
                    case null:
                        //START
                        echo \yii\helpers\Html::a('Start Listener',['/node/rpc/control-listener','action'=>'start','nl_id'=>$model->id],['class'=>'btn btn-success']);
                        break;
                    case 'STOPPING':
                    case 'STARTING':
                    case 'BACKOFF':
                        //CHILL
                    break;
                }
            ?>
        </div>
    </div>
</div>

<?php
Pjax::end();
?>