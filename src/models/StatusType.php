<?php

namespace lnpay\models;

use Yii;

/**
 * This is the model class for table "status_type".
 *
 * @property int $id
 * @property string $type
 * @property string $name
 * @property string $display_name
 *
 * @property CustyDomain[] $custyDomains
 */
class StatusType extends \yii\db\ActiveRecord
{
    const CUSTYDOMAIN_ACTIVE = 50;
    const CUSTYDOMAIN_PENDING = 51;
    const CUSTYDOMAIN_INACTIVE = 52;

    const LINK_ACTIVE = 75;
    const LINK_PASSTHRU = 80;
    const LINK_DISABLED = 85;

    const PYWL_ACTIVE = 90;
    const PYWL_INACTIVE = 95;

    const PYWL_RSS_ACTIVE = 110;
    const PYWL_RSS_INACTIVE = 115;

    const FAUCET_CREATING = 135;
    const FAUCET_ACTIVE = 140;
    const FAUCET_DISABLED = 145;
    const FAUCET_DEPLETED = 146;
    const FAUCET_PASSTHRU = 150;

    const FAUCET_HOSE_ACTIVE = 151;
    const FAUCET_HOSE_DISABLED = 152;
    const FAUCET_HOSE_DEPLETED = 153;

    const DISTRO_METHOD_ACTIVE = 170;
    const DISTRO_METHOD_INACTIVE = 175;

    const WALLET_ACTIVE = 200;
    const WALLET_INACTIVE = 210;

    const WALLET_LNURL_ACTIVE = 450;
    const WALLET_LNURL_INACTIVE = 455;

    const ORG_ACTIVE = 500;
    const ORG_INACTIVE = 510;


    const WEBHOOK_ACTIVE = 230;
    const WEBHOOK_INACTIVE = 235;

    const UAK_ACTIVE = 30;
    const UAK_INACTIVE = 35;

    const LN_NODE_ACTIVE = 300;
    const LN_NODE_INACTIVE = 305;
    const LN_NODE_ERROR = 309;

    const LN_NODE_RPC_UP = 320;
    const LN_NODE_RPC_INACTIVE = 325;
    const LN_NODE_RPC_ERROR = 329;

    const LN_NODE_REST_UP = 330;
    const LN_NODE_REST_INACTIVE = 335;
    const LN_NODE_REST_ERROR = 339;

    const LN_NODE_PROFILE_ACTIVE = 400;
    const LN_NODE_PROFILE_INACTIVE = 405;

    const TYPE_FAUCET_HOSE = 'faucet_hose';
    const TYPE_WEBHOOK = 'webhook';
    const TYPE_FAUCET = 'faucet';
    const TYPE_LN_NODE = 'ln_node';
    const TYPE_PYWL = 'pywl';
    const TYPE_ORG = 'org';

    const LN_SUBNODE_PENDING = 350;
    const LN_SUBNODE_UNLOCKING = 354;
    const LN_SUBNODE_RUNNING = 358;
    const LN_SUBNODE_LOCKED = 362;
    const LN_SUBNODE_STOPPED = 366;
    const LN_SUBNODE_DESTROYED = 370;




    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'status_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'name', 'display_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'name' => 'Name',
            'display_name' => 'Status',
        ];
    }

    public static function getAvailableStatuses($type)
    {
        return static::find()->where(['type'=>$type])->asArray()->all();
    }

    public function fields()
    {
        $fields = parent::fields();
        unset($fields['id']);

        return $fields;
    }
}
