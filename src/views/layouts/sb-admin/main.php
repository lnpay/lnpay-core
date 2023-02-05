<?php
use yii\helpers\Html;

/* (C) Copyright 2019 Heru Arief Wijaya (http://belajararief.com/) untuk Indonesia.*/

\lnpay\assets\AppSbAsset::register($this);
\hoaaah\sbadmin2\assets\SbAdmin2Asset::register($this);

$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@bower/startbootstrap-sb-admin-2');
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <?php
    /**/
    ?>
    <body id="page-top">

    <?php $this->beginBody() ?>

    <!-- Page Wrapper -->
    <div id="wrapper">

        <?= $this->render(
            'sidebar.php',
            ['directoryAsset' => $directoryAsset]
        ) ?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <?= $this->render(
                    'header.php',
                    ['directoryAsset' => $directoryAsset]
                ) ?>

                <?= $this->render(
                    'content.php',
                    ['content' => $content, 'directoryAsset' => $directoryAsset]
                ) ?>

            </div>
            <!-- End of Main Content -->


            <?= $this->render(
                'footer.php',
                ['content' => $content, 'directoryAsset' => $directoryAsset]
            ) ?>

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->


    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>