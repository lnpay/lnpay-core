<?php

namespace lnpay\models;

use lnpay\behaviors\JsonDataBehavior;
use lnpay\components\HelperComponent;
use Google\Rpc\Status;
use Yii;

/**
 * This is the model class for table "base_link".
 *
 * @property int $id
 * @property int $created_at
 * @property int $updated_at
 * @property int $rep
 * @property string $short_url
 * @property string $destination_url
 * @property int $custy_domain_id
 * @property int $status_type_id
 * @property string $json_data
 *
 * @property CustyDomain $custyDomain
 * @property StatusType $statusType
 * @property BaseLinkAnalytics[] $baseLinkAnalytics
 * @property Faucet[] $faucets
 */
class BaseLink extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'base_link';
    }

    public function behaviors()
    {
        return [
            'timestamp'     => \yii\behaviors\TimestampBehavior::className(),
            'json'=>JsonDataBehavior::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['external_hash','default','value'=>'blnk_'.HelperComponent::generateRandomString(12)],
            [['rep', 'custy_domain_id', 'status_type_id','user_id'], 'integer'],
            [['custy_domain_id'], 'default', 'value'=>CustyDomain::DEFAULT_DOMAIN_LNPAY],
            [['status_type_id'], 'default', 'value'=>StatusType::LINK_ACTIVE],
            [['short_url'], 'default', 'value'=>function($model) { return $model->generateWords; }],
            ['destination_url','url','defaultScheme' => 'https'],
            [['short_url', 'destination_url'], 'string']
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
            'rep' => 'Rep',
            'short_url' => 'Short Url',
            'destination_url' => 'Destination Url',
            'custy_domain_id' => 'Custy Domain ID',
            'status_type_id' => 'Status Type ID',
            'json_data' => 'Json Data',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustyDomain()
    {
        return $this->hasOne(CustyDomain::className(), ['id' => 'custy_domain_id']);
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
    public function getLinkLayout()
    {
        return $this->hasOne(Layout::className(), ['id' => 'link_layout_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBaseLinkAnalytics()
    {
        return $this->hasMany(BaseLinkAnalytics::className(), ['base_link_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFaucets()
    {
        return $this->hasMany(Faucet::className(), ['base_link_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPywls()
    {
        return $this->hasMany(Pywl::className(), ['base_link_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPywl()
    {
        return $this->hasOne(Pywl::className(), ['base_link_id' => 'id']);
    }

    public function generateLink()
    {
        if ($this->save()) {
            return $this;
        } else {
            throw new \Exception(HelperComponent::getFirstErrorFromFailedValidation($this));
        }
    }

    public function getGenerateWords()
    {
        return substr(md5(mt_rand()),0,6);
    }

    public function getUrl($params=[],$distro_method_name=NULL)
    {
        if ($distro_method_name === NULL)
            $distro_method_name = DistroMethod::NAME_WEB;

        $preserveBaseUrl = \LNPay::$app->urlManager->baseUrl;
        $base_url = (@$this->custyDomain->fullBaseUrl?:$preserveBaseUrl."/to/"); //if not set in DB
        \LNPay::$app->urlManager->setBaseUrl($base_url);

        $url = \LNPay::$app->urlManager->createAbsoluteUrl(["{$this->short_url}/{$distro_method_name}"]+$params);

        \LNPay::$app->urlManager->setBaseUrl($preserveBaseUrl);

        return $url;
    }

    public function getRawUrl($params=[])
    {
        if (\LNPay::$app instanceof Yii\web\Application){
            $preserveBaseUrl = \LNPay::$app->urlManager->baseUrl;
        }

        $base_url = (@$this->custyDomain->fullBaseUrl?:$preserveBaseUrl."/to/"); //if not set in DB
        \LNPay::$app->urlManager->setBaseUrl($base_url);

        $url = \LNPay::$app->urlManager->createAbsoluteUrl(["{$this->short_url}"]+$params);

        if (\LNPay::$app instanceof Yii\web\Application) {
            \LNPay::$app->urlManager->setBaseUrl($preserveBaseUrl);
        }

        return $url;
    }

    public function getProductObject()
    {
        switch ($this->link_type_id) {
            case 1:
                break;
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {

            }

            return true;
        } else {
            return false;
        }
    }



    /**
     *
     *
     * API STUFF
     *
     *
     */

    public function fields()
    {
        $fields = parent::fields();
        $fields['statusType'] = 'statusType';
        $fields['custyDomain'] = 'custyDomain';
        $fields['id'] = $fields['external_hash'];

        unset($fields['external_hash'],$fields['user_id'],$fields['rep'],$fields['json_data'],$fields['updated_iat'],$fields['link_type_id'],$fields['link_layout_id'],$fields['status_type_id'],$fields['custy_domain_id']);
        return $fields;
    }
}
