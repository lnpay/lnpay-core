<?php

namespace lnpay\models\integration;

use Yii;

/**
 * This is the model class for table "integration_service".
 *
 * @property int $id
 * @property string $name
 * @property string $display_name
 * @property string $json_data
 *
 * @property IntegrationWebhook[] $integrationWebhooks
 */
class IntegrationService extends \yii\db\ActiveRecord
{
    const SERVICE_ZAPIER = 300;
    const SERVICE_IFTTT = 310;
    const USER_WEBHOOK = 500;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'integration_service';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['json_data'], 'string'],
            [['name', 'display_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'display_name' => 'Service',
            'json_data' => 'Json Data',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIntegrationWebhooks()
    {
        return $this->hasMany(IntegrationWebhook::className(), ['integration_service_id' => 'id']);
    }
}
