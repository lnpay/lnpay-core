<?php

namespace lnpay\models\integration;

use lnpay\components\HelperComponent;
use lnpay\models\action\ActionFeed;
use lnpay\models\integration\IntegrationWebhook;
use Psr\Http\Message\ResponseInterface;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "integration_webhook_request".
 *
 * @property int $id
 * @property string $external_hash
 * @property int $created_at
 * @property int $integration_webhook_id
 * @property string $request_payload
 * @property string|null $response_body
 * @property int|null $response_status_code
 *
 * @property IntegrationWebhook $integrationWebhook
 */
class IntegrationWebhookRequest extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'integration_webhook_request';
    }

    public function behaviors()
    {
        return [
            [
                'class'=>\yii\behaviors\TimestampBehavior::class,
                'updatedAtAttribute'=>false
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['integration_webhook_id', 'request_payload'], 'required'],
            ['external_hash', 'default', 'value'=> 'iwhr_'.HelperComponent::generateRandomString(24)],
            [['integration_webhook_id', 'response_status_code','action_feed_id'], 'integer'],
            [['request_payload', 'response_body'], 'string'],
            [['external_hash'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'external_hash' => 'External Hash',
            'created_at' => 'Created At',
            'integration_webhook_id' => 'Integration Webhook ID',
            'request_payload' => 'Request Payload',
            'response_body' => 'Response Body',
            'response_status_code' => 'Response Status Code',
        ];
    }

    /**
     * Gets query for [[IntegrationWebhook]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getIntegrationWebhook()
    {
        return $this->hasOne(IntegrationWebhook::className(), ['id' => 'integration_webhook_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActionFeed()
    {
        return $this->hasOne(ActionFeed::className(), ['id' => 'action_feed_id']);
    }

    /**
     * @param ActionFeed $actionFeedObject
     * @param array $additionalData
     * @return array
     */
    public static function preparePayload(ActionFeed $actionFeedObject,$additionalData=[])
    {
        $payload = $actionFeedObject->toArray();
        $payload = ArrayHelper::merge($payload,$additionalData);

        return $payload;
    }

    /**
     * @param IntegrationWebhook $integrationWebhook
     * @param ActionFeed $actionFeedObject
     * @return IntegrationWebhookRequest
     * @throws \Exception
     */
    public static function prepareRequest(IntegrationWebhook $integrationWebhook, ActionFeed $actionFeedObject)
    {
        $log = new static();
        $log->integration_webhook_id = $integrationWebhook->id;
        $log->request_payload = json_encode(static::preparePayload($actionFeedObject));
        $log->action_feed_id = $actionFeedObject->id;
        if ($log->save())
            return $log;
        else
            throw new \Exception('Webhook log unable to create: '.$integrationWebhook->id);

    }

    /**
     * @param ResponseInterface $guzzleResponse
     * @return $this
     * @throws \Exception
     */
    public function processResponse(ResponseInterface $guzzleResponse)
    {
        $this->response_body = HelperComponent::parseHeaderArrayToString($guzzleResponse->getHeaders())."\n".$guzzleResponse->getBody()->getContents();
        $this->response_status_code = $guzzleResponse->getStatusCode();
        if ($this->save())
            return $this;
        else
            throw new \Exception('Webhook log unable to update response: '.$this->id);
    }
}
