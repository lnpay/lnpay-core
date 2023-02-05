<?php
use yii\helpers\Html;

/* (C) Copyright 2019 Heru Arief Wijaya (http://belajararief.com/) untuk Indonesia.*/

?>

<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">


    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">


        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
                <?= Html::a('Login / Signup', ['/home/login'], ['class' => 'nav-link']) ?>
        </li>

    </ul>
    <?php echo \yii\helpers\Html::a('API Docs <i class="fa fa-external-link-alt"></i>','https://docs.lnpay.co',[
        'class'=>'btn btn-primary',
        'target'=>'_blank',
        'title'=>'Use the API for basic functionality using the permissioned keys below.']); ?>

</nav>
<!-- End of Topbar -->