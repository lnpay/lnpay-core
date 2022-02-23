<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = $org->display_name;

?>
    <h1>Member Details</h1>
<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'email',
        'created_at:datetime',
        'orgUserType.display_name'
    ],
]); ?>


<?php

?>