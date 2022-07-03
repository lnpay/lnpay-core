<?php

use lnpay\models\LnNodeSearch;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel lnpay\models\LnNodeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<br/>
<div class="ln-node-index">
    <?php

        echo \yii\widgets\DetailView::widget([
            'model' => $node,
            'attributes' => [
                'alias',
                'default_pubkey',
                'network',
                [
                    'label' => 'Implementation',
                    'value' => $node->lnNodeImplementation->display_name
                ],
                [
                    'label' => 'Version',
                    'value' => $node->getInfoValueByKey('version'),
                ],
                [
                    'label' => 'Synced to chain',
                    'value' => $node->getInfoValueByKey('synced_to_chain'),
                ],
                [
                    'label' => 'Synced to graph',
                    'value' => $node->getInfoValueByKey('synced_to_graph'),
                ],
                [
                    'label' => 'Block Height',
                    'value' => $node->getInfoValueByKey('block_height'),
                ],
                [
                    'label' => 'Num Peers',
                    'value' => $node->getInfoValueByKey('num_peers'),
                ],
                [
                    'label' => 'Num Active Channels',
                    'value' => $node->getInfoValueByKey('num_active_channels'),
                ],
                [
                    'label' => 'Num Pending Channels',
                    'value' => $node->getInfoValueByKey('num_pending_channels'),
                ],
                'created_at:datetime',
                'updated_at:datetime',
            ],
        ]);
    ?>


</div>
