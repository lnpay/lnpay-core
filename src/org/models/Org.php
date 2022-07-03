<?php

namespace lnpay\org\models;

use lnpay\behaviors\JsonDataBehavior;
use lnpay\components\HelperComponent;
use lnpay\models\StatusType;
use lnpay\models\User;
use lnpay\node\models\LnNode;
use Yii;

/**
 * This is the model class for table "org".
 *
 * @property int $id
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property string $name
 * @property string $display_name
 * @property string $external_hash
 * @property int $status_type_id
 * @property string|null $json_data
 *
 * @property LnNode[] $lnNodes
 * @property StatusType $statusType
 * @property User[] $users
 */
class Org extends \yii\db\ActiveRecord
{
    const LNPAY_DEFAULT_ORG = 2000;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'org';
    }

    public function behaviors()
    {
        return [
            \yii\behaviors\TimestampBehavior::class,
            JsonDataBehavior::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[], 'required'],
            [['status_type_id'], 'integer'],
            [['external_hash'],'default','value'=>'org_'.HelperComponent::generateRandomString(8)],
            [['status_type_id'],'default','value'=>StatusType::ORG_ACTIVE],
            [['json_data'], 'safe'],
            [['name', 'display_name'], 'string', 'max' => 255],
            [['external_hash'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'name' => 'Name',
            'display_name' => 'Display Name',
            'external_hash' => 'External Hash',
            'status_type_id' => 'Status Type ID',
            'json_data' => 'Json Data',
        ];
    }

    /**
     * Gets query for [[LnNodes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLnNodes()
    {
        return $this->hasMany(LnNode::className(), ['org_id' => 'id']);
    }

    /**
     * Gets query for [[StatusType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatusType()
    {
        return $this->hasOne(StatusType::className(), ['id' => 'status_type_id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['org_id' => 'id']);
    }
}
