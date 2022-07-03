<?php
namespace lnpay\models\integration;

use yii\base\Model;

use Yii;

/**
 * Signup form
 */
class WebhookTestForm extends Model
{
    public $action_id;
    public $integration_webhook_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['action_id','integration_webhook_id'], 'required']
        ];
    }

    public function attributeLabels()
    {
        return ['action_id'=>'Event'];
    }

}
