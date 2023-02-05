<?php

use lnpay\models\integration\IntegrationWebhookSearch;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel lnpay\models\integration\IntegrationWebhookSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'RPC Event Forwarder';
$this->params['breadcrumbs'][] = $this->title;



?>

<?php
echo \yii\bootstrap4\Alert::widget([
    'body' => 'Please see '.Html::a('Webhooks: Getting Started','https://docs.lnpay.co/webhooks/getting-started',['target'=>'_blank']).' for more info',
    'options' => [
        'class' => 'alert-info',
    ],
]);

?>
    <div class="integration-webhook-index">

        <p>
            <?= Html::a('Create RPC Forwarder <i class="fas fa-arrow-right"></i>', ['/webhook/create'], ['class' => 'btn btn-success']) ?>
        </p>

        <p>
            <?= Html::a('View RPC Forwarders <i class="fas fa-arrow-right"></i>', ['/developers/webhook'], ['class' => 'btn btn-info']) ?>
        </p>

        <p>RPC Events can be configured to be forwarded to an HTTP endpoint via webhooks. <br/><br/> See the reference below for the object data that is passed through</p>

        <p><a href="https://api.lightning.community/#lnd-grpc-api-reference" target="_blank" class="btn btn-primary">LND GRPC API Reference <i class="fas fa-external-link-square-alt"></i></a></p>

        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>



    </div>
