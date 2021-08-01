<?php

use lnpay\models\integration\IntegrationWebhookSearch;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel lnpay\models\integration\IntegrationWebhookSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Events';
$this->params['breadcrumbs'][] = $this->title;



?>
<?php $this->beginContent('@app/views/layouts/sidebar/_nav-developers.php');?>

    <div class="integration-webhook-index">

        <h1><?= Html::encode($this->title) ?></h1>


        <?= GridView::widget([
            'dataProvider' => $afDp,
            'columns' => [
                'created_at:datetime',
                'external_hash',
                'actionName.display_name'


            ],
        ]); ?>

    </div>
<?php $this->endContent();?>