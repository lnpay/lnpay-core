<?php
use yii\bootstrap4\Breadcrumbs;
use yii\bootstrap4\Alert;

/* (C) Copyright 2019 Heru Arief Wijaya (http://belajararief.com/) untuk Indonesia.*/
?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">

        <?php if (isset($this->blocks['content-header'])) { ?>
            <h1 class="h3 mb-0 text-gray-800"><?= $this->blocks['content-header'] ?></h1>
        <?php } else {
            echo Breadcrumbs::widget(
                [
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]);

            ?>
        <?php } ?>

    </div>
    <!-- Content Row -->
    <div class="row">
        <div class="col-md-12">
            <?php if (Yii::$app->getSession()->getAllFlashes()) {
                foreach (Yii::$app->getSession()->getAllFlashes() as $key => $value) {
                    echo Alert::widget([
                        'options' => [
                            'class' => 'alert-' . $key,
                        ],
                        'body' => $value,
                    ]);
                }
            } ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?= $content ?>
        </div>
    </div>
</div>
<!-- /.container-fluid -->