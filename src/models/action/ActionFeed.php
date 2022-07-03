<?php

namespace lnpay\models\action;

use lnpay\components\HelperComponent;
use lnpay\events\ActionEvent;
use Yii;

/**
 * This is the model class for table "action_feed".
 *
 * @property int $id
 * @property int $created_at
 * @property int $action_name_id
 * @property int $user_id
 *
 * @property ActionData $actionData
 * @property User $user
 */
class ActionFeed extends \yii\db\ActiveRecord
{
    public $_actionData = '[]';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'action_feed';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class'=>\yii\behaviors\TimestampBehavior::className(),
                'updatedAtAttribute'=>null
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['external_hash'],'default','value'=>'evt_'.HelperComponent::generateRandomString(24)],
            [['user_id'], 'integer'],
            [['action_name_id', 'user_id'],'required']
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
            'action_name_id' => 'Action Name ID',
            'user_id' => 'User ID',
            'external_hash'=>'ID'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActionData()
    {
        $d = $this->hasOne(ActionData::className(), ['action_feed_id' => 'id'])->one()->data;
        return $d;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActionName()
    {
        return $this->hasOne(ActionName::className(), ['id' => 'action_name_id']);
    }

    public function getActionDataFlat()
    {
        if (empty($this->actionData))
            return [];

        $array = HelperComponent::array_flatten($this->actionData);

        return $array;
    }

    public function setActionData($data)
    {
        $this->_actionData = json_encode($data);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\lnpay\models\User::className(), ['id' => 'user_id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $ad = new ActionData;
            $ad->action_feed_id = $this->id;
            $ad->data = json_decode($this->_actionData,TRUE);
            if (!$ad->save())
                throw new \Exception('Unable to save action data! -- '.HelperComponent::getFirstErrorFromFailedValidation($this));
        }


        parent::afterSave($insert, $changedAttributes);
    }














    /**
     *
     *
     *
     * API STUFF
     *
     *
     *
     */

    public function fields()
    {
        $fields = parent::fields();
        $fields['id'] = $fields['external_hash'];
        $fields['event'] = 'actionName';
        $fields['data'] = 'actionData';

        unset($fields['external_hash'],$fields['action_name_id'],$fields['user_id']);

        return $fields;
    }
}
