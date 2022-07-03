<?php

use lnpay\node\models\LnNode;
use yii\helpers\Html;
use yii\web\View;

$this->title = 'Launch a Hosted Node!';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ln-node-create">

    <h1><?= Html::encode($this->title) ?></h1>
    <h2>This is extremely ALPHA!</h2>
    <p>
        <ul>
        <li>testnet only right now</li>
        <li>Fully functional node - test around</li>
        <li>Any node might get turned off or otherwise disappear at any time</li>
        <li><a href="https://testnet-faucet.mempool.co/" target="_blank">TESTNET BITCOINS TO FUND YOUR NODE!</a></li>
    </ul>
    </p>
    <?= $this->render('_form-create', [
        'model' => $model,
    ]) ?>

</div>
