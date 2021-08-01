<?php

namespace lnpay\models;

use lnpay\behaviors\JsonDataBehavior;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "base_link_analytics".
 *
 * @property int $id
 * @property int $created_at
 * @property int $updated_at
 * @property int $base_link_id
 * @property string $engagement_type
 * @property string $referrer
 * @property string $requester_ip
 * @property string $device_type
 * @property string $json_data
 *
 * @property BaseLink $baseLink
 */
class BaseLinkAnalytics extends \yii\db\ActiveRecord
{
    const E_LNURL_PREWITHDRAW_REQUEST = 'lnurl_prewithdraw_request';
    const E_CLICK = 'click';
    const E_IMPRESSION = 'impression';
    const E_LNURL_WITHDRAW_REQUEST = 'lnurl_withdraw_request';
    const E_XML_FEED_PULL = 'xml_feed_pull';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'base_link_analytics';
    }

    public function behaviors()
    {
        return [
            \yii\behaviors\TimestampBehavior::className(),
            JsonDataBehavior::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['base_link_id'], 'integer'],
            [['referrer'], 'string'],
            [['base_link_id'], 'required'],
            [['engagement_type', 'requester_ip','domain'], 'string', 'max' => 255],
            [['device_type'], 'string', 'max' => 11],
            [['base_link_id'], 'exist', 'skipOnError' => true, 'targetClass' => BaseLink::className(), 'targetAttribute' => ['base_link_id' => 'id']],
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
            'base_link_id' => 'Base Link ID',
            'engagement_type' => 'Engagement Type',
            'referrer' => 'Referrer',
            'requester_ip' => 'Requester Ip',
            'device_type' => 'Device Type',
            'json_data' => 'Json Data',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBaseLink()
    {
        return $this->hasOne(BaseLink::className(), ['id' => 'base_link_id']);
    }

    public static function createEntry($base_link_id,$engagement_type='click',$data=[])
    {
        $baseLink = BaseLink::findOne($base_link_id);
        $local_refer = false;
        $dObjects = CustyDomain::find()->where(['id'=>[CustyDomain::DEFAULT_DOMAIN_LNPAY,CustyDomain::DEFAULT_DOMAIN_ID]])->all();
        foreach ($dObjects as $cd)
            if (stripos(\LNPay::$app->request->getReferrer(),$cd->domain_name)!==FALSE) {
                return -1;
            }

        $ba = new BaseLinkAnalytics();
        $ba->base_link_id = $baseLink->id;
        $ba->engagement_type = $engagement_type;
        $ba->referrer = \LNPay::$app->request->getReferrer();
        $ba->requester_ip = \LNPay::$app->request->getUserIp();
        $ba->user_agent = \LNPay::$app->request->getUserAgent();
        $ba->domain = \LNPay::$app->request->getHostInfo();
        $ba->appendJsonData(ArrayHelper::merge(\LNPay::$app->request->getQueryParams(),$data));
        if ($b = $ba->save()) {
            return true;
        }
    }
}
