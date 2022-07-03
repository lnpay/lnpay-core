<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = $org->display_name;

?>
    <h1>Organization Details</h1>
<?php
    echo \yii\widgets\DetailView::widget([
        'model' => $org,
        'attributes' => [
            [
                'label'=>'ID',
                'value'=>function($model) { return $model->external_hash; }
            ],
            'display_name',
            'created_at:datetime',
            [
                'label'=>'Status',
                'value'=>function($model) { return $model->statusType->display_name; }
            ],
        ]
    ]);
?>


<?php

?>