<?php

namespace lnpay\node\models;

use lnpay\behaviors\JsonDataBehavior;
use lnpay\components\HelperComponent;
use lnpay\models\LnTx;
use lnpay\node\components\LndNodeConnector;
use lnpay\node\components\LnMacaroonObject;
use lnpay\components\SupervisorComponent;

use lnpay\jobs\SupervisorRemoveLndRpcConfigFileJob;
use lnpay\models\action\ActionData;
use lnpay\models\action\ActionName;
use lnpay\models\StatusType;
use lnpay\models\User;
use lnpay\org\models\Org;
use lnpay\wallet\models\Wallet;
use Yii;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "ln_node".
 *
 * @property string $id
 * @property string|null $alias
 * @property string $ln_node_implementation_id
 * @property string|null $default_pubkey
 * @property string|null $uri
 * @property string|null $host
 * @property int $rpc_port
 * @property int $rest_port
 * @property int $ln_port
 * @property string|null $tls_cert
 * @property string|null $getinfo
 * @property int|null $status_type_id
 * @property int|null $rpc_status
 * @property int|null $rest_status
 * @property string|null $json_data
 *
 * @property LnNodeImplementation $lnNodeImplementation
 * @property LnNodeProfile[] $lnNodeProfiles
 */
class LnNode extends \yii\db\ActiveRecord
{
    private $_macaroonObject;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ln_node';
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
            ['id','default','value' => strtolower('lnod_'.HelperComponent::generateRandomString(18))],
            [['id'], 'unique'],
            ['rpc_port','default','value' => LnNodeImplementation::LND_DEFAULT_GRPC_PORT],
            ['rest_port','default','value' => LnNodeImplementation::LND_DEFAULT_REST_PORT],
            ['ln_port','default','value' => LnNodeImplementation::LND_DEFAULT_LN_PORT],
            ['status_type_id','default','value'=>StatusType::LN_NODE_ACTIVE],
            ['rpc_status_id','default','value'=>StatusType::LN_NODE_RPC_UP],
            ['rest_status_id','default','value'=>StatusType::LN_NODE_REST_UP],
            ['ln_node_implementation_id','default','value' => 'lnd'],
            ['is_custodian','default','value' => 0],
            [['wallet_password','network'],'string'],
            [['org_id','rpc_port', 'rest_port', 'ln_port','internal_rpc_port', 'internal_rest_port', 'internal_ln_port', 'status_type_id', 'rpc_status_id', 'rest_status_id','user_id','onchain_total_sats','onchain_confirmed_sats'], 'integer'],
            [['tls_cert','onchain_nextaddr'], 'string'],
            [['getinfo', 'json_data'], 'safe'],
            [['id', 'alias', 'default_pubkey', 'uri', 'host'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'alias' => 'Alias',
            'ln_node_implementation_id' => 'Ln Node Implementation ID',
            'default_pubkey' => 'Default Pubkey',
            'uri' => 'Uri',
            'host' => 'Host',
            'rpc_port' => 'Rpc Port',
            'rest_port' => 'Rest Port',
            'ln_port' => 'Ln Port',
            'tls_cert' => 'Tls Cert',
            'getinfo' => 'Getinfo',
            'status_type_id' => 'Status Type ID',
            'rpc_status' => 'Rpc Status',
            'rest_status' => 'Rest Status',
            'json_data' => 'Json Data',
        ];
    }

    /**
     * Gets query for [[LnNodeImplementation]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLnNodeImplementation()
    {
        return $this->hasOne(LnNodeImplementation::className(), ['name' => 'ln_node_implementation_id']);
    }

    /**
     * Gets query for [[LnNodeProfiles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLnNodeProfiles()
    {
        return $this->hasMany(LnNodeProfile::className(), ['ln_node_id' => 'id']);
    }

    /**
     * Gets query for [[LnNodeProfiles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNodeListeners()
    {
        return $this->hasMany(NodeListener::className(), ['ln_node_id' => 'id']);
    }

    /**
     * Gets query for [[LnNodeProfiles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBaseLnNodeProfile()
    {
        return $this->hasOne(LnNodeProfile::className(), ['ln_node_id' => 'id'])->andOnCondition(['is_default'=>1]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrg()
    {
        return $this->hasOne(Org::class, ['id' => 'org_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatusType()
    {
        return $this->hasOne(StatusType::class, ['id' => 'status_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFeeWallet()
    {
        return $this->hasOne(Wallet::class, ['id' => 'fee_wallet_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKeysendWallet()
    {
        return $this->hasOne(Wallet::class, ['id' => 'keysend_wallet_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRpcStatusType()
    {
        return $this->hasOne(StatusType::class, ['id' => 'rpc_status_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRestStatusType()
    {
        return $this->hasOne(StatusType::class, ['id' => 'rest_status_id']);
    }

    /**
     * @param $macaroonObject
     */
    public function setBaseMacaroonObject(LnMacaroonObject $macaroonObject)
    {
        $this->_macaroonObject = $macaroonObject;
    }

    /**
     * Gets query for [[LnNodeProfiles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBaseMacaroonObject()
    {
        if ($this->isNewRecord) {
            return $this->_macaroonObject;
        } else {
            return $this->baseLnNodeProfile->macaroonObject;
        }
    }

    /**
     * @param $attributes
     * @return LnNodeProfile
     * @throws \Exception
     */
    public function addProfile($attributes)
    {
        $np = new LnNodeProfile();
        $np->attributes = $attributes;
        $np->user_id = $this->user_id;
        $np->ln_node_id = $this->id;
        $np->macaroon_hex = HelperComponent::encryptForDbUse($np->macaroon_hex,getenv('GENERAL_ENCRYPTION_KEY'),$this->id);
        if ($np->save()) {
            return $np;
        } else {
            throw new \Exception(HelperComponent::getFirstErrorFromFailedValidation($np));
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function getCustodialNodeQuery($user_id=null)
    {
        if ($user_id) { //if user_id is supplied, attempt to find the org's custodial node
            $user = User::findOne($user_id);
            if ($user) {
                $orgNode = static::find()->where(['org_id'=>$user->org_id,'is_custodian'=>1])->orderBy('ln_node.created_at ASC');
                if ($orgNode->exists()) { //if org node exists return, otherwise backwards compatible with base node
                    return $orgNode;
                }
            }

        }
        //return base node
        return static::find()->orderBy('ln_node.created_at ASC');
    }


    /**
     * @param $key
     * @return mixed
     */
    public function getInfoValueByKey($key)
    {
        return @$this->getinfo[$key];
    }

    /**
     * @param $invoiceOptions
     * @return mixed
     * @throws \Exception
     */
    public function tryCreateInvoice($invoiceOptions)
    {
        //We can do potential node balancing stuff here if this fails, later on
        try {
            return $this->getLndConnector('RPC')->createInvoice($invoiceOptions);
        } catch (\lnpay\node\exceptions\UnableToCreateInvoiceException $e) {
            //Try the next node
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param $payment_request
     * @return mixed
     */
    public static function decodeInvoiceHelper($payment_request)
    {
        $node = static::getCustodialNodeQuery()->one();
        return $node->getLndConnector()->decodeInvoice($payment_request);
    }


    /**
     * @param $decodedInvoiceObject
     * @return float|int
     */
    public function getFeeRate($decodedInvoiceObject)
    {
        if (is_array($decodedInvoiceObject)) {
            $decodedInvoiceObject = (object) $decodedInvoiceObject;
        }

        $num_satoshis = $decodedInvoiceObject->num_satoshis;

        $max_percent = ($this->user->getJsonData(User::DATA_MAX_NETWORK_FEE_PERCENT)?:5);

        return ceil($num_satoshis * ($max_percent/100));
    }


    /****************************************************
     * RPC STUFF / REST
     *****************************************************/

    /**
     * @return LndNodeConnector
     * @throws \Exception
     */
    public function getLndConnector($webService='RPC')
    {
        return LndNodeConnector::initConnector($this,$webService);
    }

    /**
     * @return string
     */
    public function getSupervisorConfFilename()
    {
        return $this->id.'.conf';
    }

    /**
     * @throws \Exception
     */
    public function spawnLndRpcSubscribers()
    {
        \LNPay::info($this->id.': Spawning subscribers');
        NodeListener::createLndRpcListenerObjects($this->id);
        sleep(5); //Sometimes it takes a second to write to these configs
        $this->startLndRpcSubscribers();

    }

    public function startLndRpcSubscribers()
    {
        foreach (NodeListener::find()->where(['ln_node_id'=>$this->id])->all() as $nL) {
            if ($nL->supervisor_parameters['autostart']) {
                SupervisorComponent::startProcess($nL->id);
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function removeLndRpcSubscribers()
    {
        foreach ($this->nodeListeners as $nL) {
            $nL->delete();
        }

        \LNPay::$app->queue->push(new SupervisorRemoveLndRpcConfigFileJob([
            'file_name' => $this->supervisorConfFilename
        ]));
    }

    /****************************************************
     * END RPC STUFF
     *****************************************************/



    /****************************************************
     * START HEALTH CHECKS
     *****************************************************/

    /**
     * @return bool
     */
    public function getIsRpcUp()
    {
        if ($this->rpc_status_id == StatusType::LN_NODE_RPC_UP)
            return true;
        else
            return false;
    }

    /**
     * @return bool
     */
    public function getIsRestUp()
    {
        if ($this->rest_status_id == StatusType::LN_NODE_REST_UP)
            return true;
        else
            return false;
    }

    /**
     * @param $data
     * @return bool
     */
    public function setRestErrorState($data)
    {
        if ($this->rest_status_id == StatusType::LN_NODE_REST_UP) {
            $this->rest_status_id = StatusType::LN_NODE_REST_ERROR;
            $this->appendJsonData(['rest_error_message' => $data['message'], 'rest_last_check' => time()]);
            return $this->save();
        }

        return NULL; //no change to state, already set appropriately
    }

    /**
     * @param array $data
     * @return bool
     */
    public function setRestUpState($data=[])
    {
        if ($this->rest_status_id == StatusType::LN_NODE_REST_ERROR) {
            $this->rest_status_id = StatusType::LN_NODE_REST_UP;
            $this->deleteJsonData(['rest_error_message']);
            return $this->save();
        }

        return NULL; //no change to state, already set appropriately
    }

    /**
     * @param $data
     * @return bool
     */
    public function setRpcErrorState($data)
    {
        if ($this->rpc_status_id == StatusType::LN_NODE_RPC_UP) {
            $this->rpc_status_id = StatusType::LN_NODE_RPC_ERROR;
            $this->appendJsonData(['rpc_error_message' => $data['message'], 'rpc_last_check' => time()]);
            $save = $this->save();
            $this->user->registerAction(ActionName::LN_NODE_CONNECTION_ERROR,['lnod'=>$this->toArray()]);

            return $save;
        }

        return NULL; //no change to state, already set appropriately
    }

    /**
     * @param array $data
     * @return bool
     */
    public function setRpcUpState($data=[])
    {
        if ($this->rpc_status_id == StatusType::LN_NODE_RPC_ERROR) {
            $this->rpc_status_id = StatusType::LN_NODE_RPC_UP;
            $this->deleteJsonData(['rpc_error_message']);
            return $this->save();
        }

        return NULL; //no change to state, already set appropriately
    }


    /**
     * @param $webService
     * @return mixed
     * @throws \Exception
     */
    public function healthCheck($webService)
    {
        $info = $this->getLndConnector($webService)->getInfo();
        $gi = json_decode($info,TRUE);

        switch ($webService) {
            case 'REST':
                $this->appendJsonData(['rest_last_check'=>time()]);
                if ($gi) { //UP!
                    //Update with most recent info
                    $this->uri = (@$gi['uris']?$gi['uris'][0]:'');
                    $this->alias = (@$gi['alias']?:$this->alias);
                    $this->default_pubkey = (@$gi['identity_pubkey']?:$this->default_pubkey);
                    $this->getinfo = $gi;
                    $this->save();
                    $this->setRestUpState();
                } else { //DOwn!
                    $this->setRestErrorState(['message'=>$info]);
                }
                break;
            case 'RPC':
                $this->appendJsonData(['rpc_last_check' => time()]);
                if ($gi) { //UP!
                    $this->setRpcUpState();
                } else { //DOwn!
                    $this->setRpcErrorState(['message'=>$info]);
                }
                break;
        }

        if ($gi) { //update chain stuff
            $balances = $this->getLndConnector()->walletBalance();
            $this->onchain_confirmed_sats = @$balances['confirmedBalance'] ?? 0;
            $this->onchain_unconfirmed_sats = @$balances['unconfirmedBalance'] ?? 0;
            $this->onchain_total_sats = @$balances['totalBalance'] ?? 0;

            if (!$this->onchain_nextaddr) {
                $r = $this->getLndConnector()->newAddress();
                $this->onchain_nextaddr = $r['address'];
            }

            try {
                $gi['balances'] = $this->getLndConnector()->channelBalance();
                $gi['channels'] = $this->getLndConnector()->listChannels()['channels'];
            } catch (\Throwable $t) {
                \LNPay::error($t->getMessage(),__METHOD__);
            }


            $gi['max_send'] = 0;
            $gi['max_receive'] = 0;
            if (isset($gi['channels'])) {
                foreach ($gi['channels'] as $channel) {
                    if (isset($channel['active'])) { //channel is active
                        if ( ($channel['localBalance'] ?? 0) > $gi['max_send']) {
                            $gi['max_send'] = $channel['localBalance'];
                        }
                        if ( ($channel['remoteBalance'] ?? 0) > $gi['max_receive']) {
                            $gi['max_receive'] = $channel['remoteBalance'];
                        }
                    }
                }
            }

        }

        switch ($this->ln_node_implementation_id) {
            case LnNodeImplementation::LND_SUBNODE:
                if ($gi) {
                    $this->status_type_id = StatusType::LN_SUBNODE_RUNNING;
                } else {
                    $this->status_type_id = StatusType::LN_SUBNODE_STOPPED;
                }
                break;
        }

        $this->save();
        return $gi;
    }



    /****************************************************
     * END HEALTH CHECKS
     *****************************************************/



    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            //Add our default fee catch wallet
            $wallet = new Wallet();
            $wallet->user_label = '['.$this->id.'] Network Fees';
            $wallet->user_id = $this->user_id;
            $wallet->ln_node_id = $this->id;
            $wallet->save();

            $this->fee_wallet_id = $wallet->id;
            $this->save();

            //add our default keysend catch wallet
            $wallet = new Wallet();
            $wallet->user_label = '['.$this->id.'] Inbound Keysend';
            $wallet->user_id = $this->user_id;
            $wallet->ln_node_id = $this->id;
            $wallet->save();

            $this->keysend_wallet_id = $wallet->id;
            $this->save();

            switch ($this->ln_node_implementation_id) {
                default:
                    $this->user->registerAction(ActionName::LN_NODE_USER_ADD,['lnod'=>$this->toArray()]);
            }

        }

        parent::afterSave($insert, $changedAttributes);
    }

    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        \LNPay::$app->db->createCommand()->update('ln_node', ['fee_wallet_id' => NULL], ['id'=>$this->id])->execute();
        \LNPay::$app->db->createCommand()->update('ln_node', ['keysend_wallet_id' => NULL], ['id'=>$this->id])->execute();

        LnTx::updateAll(['ln_node_id'=>NULL],['ln_node_id'=>$this->id]);

        Wallet::updateAll(['ln_node_id'=>NULL],['ln_node_id'=>$this->id]);

        NodeListener::deleteAll(['ln_node_id'=>$this->id]);

        LnNodeProfile::deleteAll(['ln_node_id'=>$this->id]);


        //This is here instead of ActionComponent::ActionName::LN_NODE_USER_REMOVE
        //because we don't want to delete unless success on remove from supervisor
        $this->removeLndRpcSubscribers();
        return true;
    }

    public function afterDelete()
    {
        parent::afterDelete();
        $this->user->registerAction(ActionName::LN_NODE_USER_REMOVE,['lnod'=>$this->toArray()]);
    }













    /**
     *
     *
     *
     * API STUFF
     */

    public function fields()
    {
        $fields = parent::fields();
        $fields['passThru'] = 'json_data';
        $fields['fee_wallet_id'] = function ($model) {
            return $this->feeWallet->external_hash;
        };
        $fields['keysend_wallet_id'] = function ($model) {
            return $this->keysendWallet->external_hash;
        };

        unset(  $fields['tls_cert'],
                $fields['getinfo'],
                $fields['user_id'],
                $fields['json_data'],
                $fields['updated_at'],
                $fields['status_type_id'],
                $fields['rest_status_id'],
                $fields['rpc_status_id'],
                $fields['internal_rpc_port'],
                $fields['internal_rest_port'],
                $fields['internal_ln_port'],
                $fields['wallet_password'],
                $fields['ln_node_implementation_id']
        );


        return $fields;
    }
}
