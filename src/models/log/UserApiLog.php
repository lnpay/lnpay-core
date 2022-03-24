<?php

namespace lnpay\models\log;

use lnpay\models\User;
use Yii;

/**
 * This is the model class for table "user_api_log".
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $external_hash
 * @property string|null $api_key
 * @property string|null $ip_address
 * @property string|null $sdk
 * @property string|null $method
 * @property string|null $base_url
 * @property string|null $request_path
 * @property string|null $request_body
 * @property string|null $request_headers
 * @property int|null $status_code
 * @property string|null $response_body
 * @property string|null $response_headers
 *
 * @property User $user
 */
class UserApiLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_api_log';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'external_hash' => 'External Hash',
            'api_key' => 'Api Key',
            'ip_address' => 'Ip Address',
            'sdk' => 'Sdk',
            'method' => 'Method',
            'base_url' => 'Base Url',
            'request_path' => 'Request Path',
            'request_body' => 'Request Body',
            'request_headers' => 'Request Headers',
            'status_code' => 'Status Code',
            'response_body' => 'Response Body',
            'response_headers' => 'Response Headers',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getAmplitudeAttributeValues()
    {
        $arr = [
            'api_key',
            'external_hash',
            'ip_address',
            'method',
            'sdk',
            'base_url',
            'status_code',
            'request_path'
        ];

        $array = [];
        foreach ($arr as $a) {
            $array[$a] = $this->{$a};
        }

        return $array;

    }
}

