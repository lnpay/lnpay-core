<?php

namespace lnpay\org\models;

use lnpay\models\User;
use Yii;

/**
 * This is the model class for table "org_user_type".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $display_name
 *
 * @property User[] $users
 */
class OrgUserType extends \yii\db\ActiveRecord
{
    const TYPE_OWNER = 20;
    const TYPE_ADMIN = 30;
    const TYPE_DEVELOPER = 40;
    const TYPE_APP_USER = 50;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'org_user_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
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
            'display_name' => 'Display Name',
        ];
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['org_user_type_id' => 'id']);
    }
}
