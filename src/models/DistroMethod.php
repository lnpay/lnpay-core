<?php

namespace lnpay\models;

use Yii;

/**
 * This is the model class for table "distro_method".
 *
 * @property int $id
 * @property string $name
 * @property string $display_name
 *
 * @property FaucetDistro[] $faucetDistros
 */
class DistroMethod extends \yii\db\ActiveRecord
{
    const RAW_LNURL = 698;
    const QR = 699;
    const WEB = 700;
    const LN_URI = 701;
    const IMAGE = 710;
    const EMAIL = 720;
    const API = 730;
    const PRINT = 740;

    const NAME_RAW_LNURL = 'raw_lnurl';
    const NAME_WEB = 'web';
    const NAME_IMAGE = 'image';
    const NAME_API = 'api';
    const NAME_EMAIL = 'email';
    const NAME_PRINT = 'print';
    const NAME_QR = 'qr';
    const NAME_LN_URI = 'ln_uri';

    const NAME_RSS = 'rss';
    const NAME_ATOM = 'atom';



    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'distro_method';
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
            'display_name' => 'Distro Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFaucetDistros()
    {
        return $this->hasMany(FaucetDistro::className(), ['distro_method_id' => 'id']);
    }

    public static function getAvailableDistroMethodsQuery()
    {
        return static::find();
    }

    public static function findByName($distro_name)
    {
        return static::find()->where(['name'=>$distro_name])->one();
    }
}
