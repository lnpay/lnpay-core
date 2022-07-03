<?php $this->beginContent('@app/views/layouts/sidebar/_nav-developers.php');

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dqDp yii\data\ActiveDataProvider */

$this->title = 'Domains';
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="paywalls-header">
        <h2 style="margin-top: 0px;">Domains</h2>
    </div>
    <div class="col-md-3">
        <a href="/domain/create"><button class="styled-button-success">Add Domain <i class="fa fa-plus-circle"></i></button></a>
    </div>

    <div class="col-md-12 container">


        <?= \yii\grid\GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                    'external_hash',
                'domain_name',
                'display_name',
                'statusType.display_name',

            ],
        ]); ?>
    </div>




<?php $this->endContent();?>