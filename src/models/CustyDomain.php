<?php

namespace lnpay\models;

use lnpay\components\HelperComponent;
use Yii;

/**
 * This is the model class for table "custy_domain".
 *
 * @property int $id
 * @property int $user_id
 * @property string $domain_name
 * @property int $use_https
 * @property string $ssl_info
 * @property int $use_hsts
 * @property int $upgrade_insecure
 * @property int $status_type_id
 * @property string $data
 *
 * @property User $user
 * @property StatusType $statusType
 * @property Link[] $links
 */
class CustyDomain extends \yii\db\ActiveRecord
{
    const DEFAULT_DOMAIN_ID = 1000;
    const DEFAULT_DOMAIN_LNPAY = 1001;
    const DEFAULT_DOMAIN_2 = 1002;
    const DEFAULT_DOMAIN_3 = 1003;
    const DEFAULT_DOMAIN_4 = 1004;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'custy_domain';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'use_https', 'use_hsts', 'upgrade_insecure', 'status_type_id'], 'integer'],
            [['ssl_info', 'data'], 'string'],
            [['status_type_id'],'default','value'=>StatusType::CUSTYDOMAIN_ACTIVE],
            [['use_https'],'default','value'=>1],
            [['external_hash'],'default','value'=>'cdom_'.HelperComponent::generateRandomString(8)],
            [['display_name','domain_name'], 'required'],
            [['domain_name'], 'string', 'max' => 255]
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
            'domain_name' => 'Domain Name',
            'use_https' => 'Use Https',
            'ssl_info' => 'Ssl Info',
            'use_hsts' => 'Use Hsts',
            'upgrade_insecure' => 'Upgrade Insecure',
            'status_type_id' => 'Status Type ID',
            'data' => 'Data',
            'external_hash'=>'ID'
        ];
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
    public function getStatusType()
    {
        return $this->hasOne(StatusType::className(), ['id' => 'status_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLinks()
    {
        return $this->hasMany(Link::className(), ['custy_domain_id' => 'id']);
    }

    public static function defaultDomains()
    {
        return [self::DEFAULT_DOMAIN_LNPAY,self::DEFAULT_DOMAIN_ID,self::DEFAULT_DOMAIN_2,self::DEFAULT_DOMAIN_3,self::DEFAULT_DOMAIN_4];
    }

    public static function getActiveDomains()
    {
        return CustyDomain::find()->where(['status_type_id'=>StatusType::CUSTYDOMAIN_ACTIVE]);
    }

    public static function findByHash($external_hash)
    {
        return static::find()->where(['external_hash'=>$external_hash])->one();
    }

    public static function isDefaultDomain($id)
    {
        if (in_array($id,self::defaultDomains()))
            return true;
        else
            return false;
    }

    public function getFullBaseUrl($forLinkCreation=true)
    {
        $base_url = '';
        if ($this->use_https)
            $base_url = 'https://';
        else
            $base_url = 'http://';

        $base_url .= $this->domain_name;

        if ($this->port != 80)
            $base_url .= ':'.$this->port;

        if (in_array($this->id,[CustyDomain::DEFAULT_DOMAIN_ID]) && $forLinkCreation)
            $base_url .= '/to';
        else if (in_array($this->id,[CustyDomain::DEFAULT_DOMAIN_LNPAY]) && $forLinkCreation)
            $base_url .= '/t';

        $base_url .= '/';

        return $base_url;
    }

    public function getRoutingEntryKey($path='')
    {
        $base_url = '//';
        $base_url .= $this->domain_name;

        if ($this->port != 80)
            $base_url .= ':'.$this->port;

        $base_url .= '/'.$path;

        return $base_url;
    }






    public function fields()
    {
        $fields = parent::fields();
        $fields['id'] = $fields['external_hash'];

        unset($fields['external_hash'], $fields['user_id'], $fields['port'],$fields['use_https'],$fields['ssl_info'],$fields['use_hsts'],$fields['upgrade_insecure'],$fields['status_type_id'],$fields['data']);
        return $fields;
    }
}
