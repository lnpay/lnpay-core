<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model lnpay\models\integration\IntegrationWebhook */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="integration-webhook-form col-md-12 well">

    <?php
        $form = ActiveForm::begin();

        $actions = \lnpay\models\action\ActionName::getActiveWebhookArrayByType();

        $hideTypes = ['ln_node','ln_subnode','user','paywall','faucet'];


        $this->registerJs('
           $(":checkbox[value=default_all]").click(function(){
                $(\'input:checkbox\').not(this).prop(\'checked\', this.checked);
            });
        ');

        ?>

    <?= $form->field($model, 'endpoint_url')->textInput(['maxlength' => true]) ?>

    <?php

    foreach ($actions as $type => $name) {
        if (in_array($type,$hideTypes))
            continue;

        echo '<div class="col-md-3">';
        echo '<h3>'.$type.'</h3>';
        echo $form->field($model, 'action_name_id['.$type.']')->label(false)
            ->checkboxList(yii\helpers\ArrayHelper::map($name, 'name', 'display_name'));
        echo '</div>';
        foreach ($name as $arr) {
            if ($model->action_name_id && in_array($arr['name'],$model->action_name_id)) {
                $this->registerJs('$(":checkbox[value=' . $arr['name'] . ']").prop(\'checked\',\'checked\');');
            }
        }

    }



    ?>
    <?= $form->field($model, 'secret')->passwordInput()->label('Secret')->hint('Header X-LNPay-Signature will be sent using HMAC SHA256 of the payload using this secret') ?>
    <?php  echo $form->field($model, 'status_type_id')->dropDownList(\yii\helpers\ArrayHelper::map(\lnpay\models\StatusType::getAvailableStatuses(\lnpay\models\StatusType::TYPE_WEBHOOK),'id','display_name')); ?>





    <?php // $form->field($model, 'status_type_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php if ($model->id) { ?>
    <div class="integration-webhook-form col-md-6 well">
        <?php
        $webhookTestForm = new \lnpay\models\integration\WebhookTestForm();
        $webhookTestForm->integration_webhook_id = $model->external_hash;
        $events = \yii\helpers\ArrayHelper::map(\lnpay\components\ActionComponent::getAvailableTestActionObjects(),'name','display_name');

        $form = ActiveForm::begin(
            ['action' => 'test-webhook']
        );

        ?>
        <?= $form->field($webhookTestForm, 'action_id')->dropDownList($events); ?>
        <?= $form->field($webhookTestForm, 'integration_webhook_id')->hiddenInput()->label(false); ?>

        <div class="form-group">
            <?= Html::submitButton('Send Test', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>


    <?php

}

    ?>