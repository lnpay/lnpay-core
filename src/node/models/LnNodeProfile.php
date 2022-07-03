<?php

namespace lnpay\node\models;

use lnpay\behaviors\JsonDataBehavior;
use lnpay\components\HelperComponent;
use lnpay\node\components\LnMacaroonObject;
use lnpay\models\StatusType;
use LndConnect\LndConnect;
use Yii;

/**
 * This is the model class for table "ln_node_profile".
 *
 * @property string $id
 * @property string $ln_node_id
 * @property int|null $is_default
 * @property string $user_label
 * @property int|null $status_type_id
 * @property string|null $macaroon
 * @property string|null $username
 * @property string|null $password
 * @property string|null $access_key
 *
 * @property LnNode $lnNode
 * @property StatusType $statusType
 */
class LnNodeProfile extends \yii\db\ActiveRecord
{
    public $submitted_perms = [];


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ln_node_profile';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            \yii\behaviors\TimestampBehavior::className(),
            [
                'class'=>JsonDataBehavior::class
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['id','default','value' => 'lnodpr_'.HelperComponent::generateRandomString(8)],
            [['is_default', 'status_type_id'], 'integer'],
            ['status_type_id','default','value'=>StatusType::LN_NODE_PROFILE_ACTIVE],
            [['macaroon_hex','json_data'], 'string'],
            ['macaroon_hex','verify_macaroon'],
            [['id', 'ln_node_id', 'user_label', 'username', 'password', 'access_key'], 'string', 'max' => 255],
            [['id'], 'unique'],
            [['submitted_perms'],'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ln_node_id' => 'Ln Node ID',
            'is_default' => 'Is Default',
            'user_label' => 'Label for your reference',
            'status_type_id' => 'Status Type ID',
            'macaroon' => 'Macaroon',
            'username' => 'Username',
            'password' => 'Password',
            'access_key' => 'Access Key',
        ];
    }

    /**
     * Gets query for [[LnNode]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLnNode()
    {
        return $this->hasOne(LnNode::className(), ['id' => 'ln_node_id']);
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
     * @return LnMacaroonObject
     */
    public function getMacaroonObject()
    {
        return new LnMacaroonObject($this->getDecryptedMacaroonHex());
    }

    public function getDecryptedMacaroonHex()
    {
        //if encrypted
        if ($dec = HelperComponent::decryptForDbUse($this->macaroon_hex,getenv('GENERAL_ENCRYPTION_KEY'),$this->ln_node_id)) {
            return $dec;
        } else { //for backwards compatibility
            return $this->macaroon_hex;
        }
    }

    public function getLndConnectString()
    {
        return LndConnect::encode($this->lnNode->host.':'.$this->lnNode->rpc_port,hex2bin($this->lnNode->tls_cert),$this->macaroonObject->hex);
    }

    /**
     * Verify that the macaroon was baked properly
     * @param $attribute_name
     * @param $params
     */
    public function verify_macaroon($attribute_name, $params)
    {
        $m = new LnMacaroonObject($this->getDecryptedMacaroonHex());

        if (!$m->isValidMacaroon) {
            $this->addError($attribute_name,'Invalid macaroon!');
        }
    }

    public static function getMacaroonCheckboxList()
    {
        $arr = [];
        foreach (LnMacaroonObject::getAllowedPermissionMap() as $entity => $actions) {
            foreach ($actions as $a) {
                $arr[$entity.'_'.$a] = $entity.'_'.$a;
            }
        }

        return $arr;
    }


    /**
     * @return LnNodeProfile|bool
     * @throws \Exception
     */
    public function bakeMacaroon()
    {
        $perms = [];
        foreach ($this->submitted_perms as $entity_action) {
            list($entity,$action) = explode("_",$entity_action);
            $p = new \Lnrpc\MacaroonPermission();
            $p->setEntity($entity);
            $p->setAction($action);
            $perms[] = $p;
        }

        $result = $this->lnNode->getLndConnector('RPC')->bakeMacaroon($perms);

        $this->macaroon_hex = $result;

        return $this->lnNode->addProfile($this->attributes);
    }
}
