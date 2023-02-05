<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model lnpay\models\CustyDomain */

$this->title = 'Create Domain';
$this->params['breadcrumbs'][] = ['label' => 'Domain', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="domain-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
