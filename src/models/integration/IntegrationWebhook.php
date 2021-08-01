<?php

namespace lnpay\models\integration;

use lnpay\behaviors\JsonDataBehavior;
use lnpay\components\ActionComponent;
use lnpay\components\HelperComponent;
use lnpay\models\action\ActionFeed;
use lnpay\models\integration\IntegrationWebhookRequest;
use lnpay\models\StatusType;
use Yii;

use \lnpay\models\action\ActionName;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "integration_webhook".
 *
 * @property int $id
 * @property int $user_id
 * @property int $link_id
 * @property int $action_name_id
 * @property int $integration_service_id
 * @property string $http_method
 * @property string $endpoint_url
 * @property string $json_data
 *
 * @property ActionName $actionName
 * @property Link $link
 * @property User $user
 * @property IntegrationService $integrationService
 */
class IntegrationWebhook extends \yii\db\ActiveRecord
{
    const DEFAULT_ALL = 'default_all';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'integration_webhook';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            \yii\behaviors\TimestampBehavior::className(),
            JsonDataBehavior::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['endpoint_url'], 'required'],
            ['action_name_id','checkIsValidActionNameArray'],
            ['integration_service_id', 'default', 'value' => IntegrationService::USER_WEBHOOK],
            ['http_method', 'default', 'value' => 'POST'],
            ['content_type', 'default', 'value' => 'application/json'],
            ['external_hash', 'default', 'value' => 'iwh_'.HelperComponent::generateRandomString(14)],
            ['status_type_id','default','value'=>StatusType::WEBHOOK_ACTIVE],
            [['user_id', 'integration_service_id','status_type_id'], 'integer'],
            [['content_type','secret'], 'string'],
            [['http_method', 'endpoint_url'], 'string', 'max' => 255],
            ['endpoint_url','url']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'action_name_id' => 'Action Name ID',
            'integration_service_id' => 'Integration Service ID',
            'http_method' => 'Http Method',
            'endpoint_url' => 'Endpoint Url',
            'json_data' => 'Json Data',
            'external_hash'=>'Webhook ID',
            'status_type_id' => 'Status Type'
        ];
    }

    /**
     * @param $attribute
     * @param $params
     * @return bool
     */
    public function checkIsValidActionNameArray($attribute,$params)
    {
        if (!is_array($this->$attribute)) {
            $this->addError($attribute,'Must supply array of event names');
            return false;
        }

        $an = ArrayHelper::map(ActionName::find()->asArray()->all(),'name','name');

        if ($this->$attribute == [self::DEFAULT_ALL])
            return true;

        foreach ($this->$attribute as $action_name) {
            if (!in_array($action_name,$an))
                $this->addError($attribute,"Invalid event: ".$action_name);
        }
    }

    public function getActionNameObjects()
    {
        $arr = [];
        foreach ($this->action_name_id as $id) {
            $arr[] = ActionName::find()->where(['name'=>$id])->one();
        }
        return $arr;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActionName()
    {
        return $this->hasOne(ActionName::className(), ['id' => 'action_name_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatusType()
    {
        return $this->hasOne(StatusType::className(), ['id' => 'status_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWebhookRequests()
    {
        return $this->hasMany(IntegrationWebhookRequest::class, ['integration_webhook_id' => 'id'])->addOrderBy('created_at DESC');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIntegrationService()
    {
        return $this->hasOne(IntegrationService::className(), ['id' => 'integration_service_id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
           ActionComponent::webhookPing($this);
        }
    }





    /**
     *
     *
     *
     *
     *
     * API STUFF
     */

    public function fields()
    {
        $fields = parent::fields();

        // remove fields that contain sensitive information
        unset($fields['user_id']);

        return $fields;
    }
}
