<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Account Details';
$this->beginContent('@app/views/layouts/sidebar/_nav-account.php');
?>
<?php
$userId = \LNPay::$app->user->identity->external_hash;
if (!\LNPay::$app->user->identity->isActivated) { ?>
    <div class="jumbotron well">
    <h2>Account Pending Activation</h2>
        <p>
            <a href="mailto:admin@lnpay.co?subject=account-verify+<?=$userId;?>" class="btn btn-primary">Request Verification <i class="glyphicon glyphicon-email"></i></a>
        </p>
    </div>

<?php } ?>

<?php
    echo \yii\widgets\DetailView::widget([
        'model' => $userModel,
        'attributes' => [
                [
                        'label'=>'ID',
                        'value'=>function($model) { return $model->external_hash; }
                ],
                'created_at:datetime',
                'tz',
                'email',
                [
                    'label'=>'Activated',
                    'value'=>function($model) { return $model->isActivated?:'Pending Activation'; }
                ],
        ]
    ]);
?>

<?php
$this->endContent();
?>