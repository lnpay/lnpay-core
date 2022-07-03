<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model lnpay\node\models\LnNode */

$this->title = 'Add Your Node to get started!';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ln-node-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'submittedMacaroonObject'=>$submittedMacaroonObject,
        'nodeInfo'=>$nodeInfo
    ]) ?>

</div>
