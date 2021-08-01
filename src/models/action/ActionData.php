<?php

namespace lnpay\models\action;

use Yii;

/**
 * This is the model class for table "action_data".
 *
 * @property int $action_feed_id
 * @property string $data
 *
 * @property ActionFeed $actionFeed
 */
class ActionData extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'action_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['action_feed_id'], 'required'],
            [['action_feed_id'], 'integer'],
            [['action_feed_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'action_feed_id' => 'Action Feed ID',
            'data' => 'Data',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActionFeed()
    {
        return $this->hasOne(ActionFeed::className(), ['id' => 'action_feed_id']);
    }
}
