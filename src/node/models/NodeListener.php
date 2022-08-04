<?php

namespace lnpay\node\models;

use lnpay\behaviors\JsonDataBehavior;
use lnpay\components\HelperComponent;
use lnpay\components\SupervisorComponent;
use lnpay\jobs\SupervisorUpdateLndRpcConfigFileJob;
use lnpay\jobs\SupervisorWriteLndRpcConfigFileJob;
use lnpay\models\action\ActionName;
use lnpay\models\StatusType;
use lnpay\models\User;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "node_listener".
 *
 * @property resource $id
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property string|null $ln_node_id
 * @property int|null $btc_node_id
 * @property int $user_id
 * @property string $config_filename
 * @property int|null $status_type_id
 * @property string|null $supervisor_parameters
 *
 * @property LnNode $lnNode
 * @property User $user
 * @property StatusType $statusType
 */
class NodeListener extends \yii\db\ActiveRecord
{
    const LND_RPC_SUBSCRIBE_TRANSACTIONS        = 'SubscribeTransactions';
    const LND_RPC_SUBSCRIBE_PEER_EVENTS         = 'SubscribePeerEvents';
    const LND_RPC_SUBSCRIBE_CHANNEL_EVENTS      = 'SubscribeChannelEvents';
    const LND_RPC_SUBSCRIBE_INVOICES            = 'SubscribeInvoices';
    const LND_RPC_SUBSCRIBE_CHANNEL_GRAPH       = 'SubscribeChannelGraph';
    const LND_RPC_SUBSCRIBE_CHANNEL_BACKUPS     = 'SubscribeChannelBackups';
    const LND_RPC_SUBSCRIBE_HTLC_EVENTS         = 'SubscribeHtlcEvents';

    public static function getAvailableSubscribeMethods()
    {
        return [
            self::LND_RPC_SUBSCRIBE_INVOICES,
            self::LND_RPC_SUBSCRIBE_CHANNEL_BACKUPS,
            self::LND_RPC_SUBSCRIBE_CHANNEL_EVENTS,
            self::LND_RPC_SUBSCRIBE_CHANNEL_GRAPH,
            self::LND_RPC_SUBSCRIBE_PEER_EVENTS,
            self::LND_RPC_SUBSCRIBE_TRANSACTIONS,
            self::LND_RPC_SUBSCRIBE_HTLC_EVENTS
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'node_listener';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            \yii\behaviors\TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id'], 'required'],
            [[ 'btc_node_id', 'user_id', 'status_type_id'], 'integer'],
            [['supervisor_parameters'], 'safe'],
            [['id', 'config_filename','ln_node_id'], 'string', 'max' => 255],
            [['id'], 'unique'],
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
            'ln_node_id' => 'Ln Node ID',
            'btc_node_id' => 'Btc Node ID',
            'user_id' => 'User ID',
            'config_filename' => 'Config Filename',
            'status_type_id' => 'Status Type ID',
            'supervisor_parameters' => 'Supervisor Parameters',
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
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
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
     * returns Supervisor compatible program config
     * @param $nodeObject
     * @param $method
     * @return array
     */
    public static function createLndRpcSupervisorConfig($nodeObject,$method)
    {
        switch ($method) {
            case static::LND_RPC_SUBSCRIBE_CHANNEL_BACKUPS:
            case static::LND_RPC_SUBSCRIBE_CHANNEL_GRAPH:
            case static::LND_RPC_SUBSCRIBE_PEER_EVENTS:
            case static::LND_RPC_SUBSCRIBE_HTLC_EVENTS:
            case static::LND_RPC_SUBSCRIBE_TRANSACTIONS:
            case static::LND_RPC_SUBSCRIBE_CHANNEL_EVENTS:
                $overrides = [
                    'autostart'=>false
                ];
                break;
            default:
                $overrides = [];
        }

        return ArrayHelper::merge([
            'command' => getenv('PHP_BIN_PATH').' '.getenv('SUPERVISOR_SERVER_APP_PATH').'yii rpc-listener/lnd-subscribe '.$nodeObject->id.' '.$method,
            'autostart'=>true,
            'autorestart'=>true,
            //'numprocs'=>1,
            //'process_name'='%(program_name)s_%(process_num)02d',
            'stderr_logfile'=>getenv('SUPERVISOR_SERVER_APP_PATH').'runtime/supervisor/'.$nodeObject->id.'.err.log',
            'stdout_logfile'=>getenv('SUPERVISOR_SERVER_APP_PATH').'runtime/supervisor/'.$nodeObject->id.'.out.log',
            'stdout_logfile_maxbytes'=>1000000, //1 MB log max
            'stdout_logfile_backups'=>0
        ],$overrides);
    }

    /**
     * @param $nodeObject
     * @param $method
     * @return NodeListener
     * @throws \Exception
     */
    public static function createLndRpcListenerObject($nodeObject,$method)
    {
        $listener = new static();
        $listener->id = $nodeObject->id.'--'.$method;
        $listener->method = $method;
        $listener->ln_node_id = $nodeObject->id;
        $listener->user_id = $nodeObject->user_id;
        $listener->config_filename = $nodeObject->id.'.conf';
        $listener->supervisor_parameters = static::createLndRpcSupervisorConfig($nodeObject,$method);
        if ($listener->save())
            return $listener;
        else
            throw new \Exception(HelperComponent::getFirstErrorFromFailedValidation($listener));
    }

    /**
     * @param $node_id
     * @return array
     * @throws \Exception
     */
    public static function createLndRpcListenerObjects($node_id)
    {
        $node = LnNode::findOne($node_id);
        $array = [];
        foreach (static::getAvailableSubscribeMethods() as $method) {
            $listener = static::createLndRpcListenerObject($node,$method);
            $array[] = $listener->id;
        }

        \LNPay::$app->queue->push(new SupervisorWriteLndRpcConfigFileJob([
            'config_filename' => $node->supervisorConfFilename,
            'listeners' => $array
        ]));

        return $array;
    }

    /**
     * @param $array
     * @return bool
     * @throws \Exception
     */
    public function updateSupervisorParameters($array)
    {
        \LNPay::$app->queue->push(new SupervisorUpdateLndRpcConfigFileJob([
            'listener_id' => $this->id,
            'parameters' => ArrayHelper::merge($this->supervisor_parameters,$array)
        ]));
    }

    /**
     * @return array|bool
     */
    public function getSupervisorProcessInfo()
    {
        return SupervisorComponent::getProcessInfo($this->id);
    }

    public function getIsRunning()
    {
        $info = $this->getSupervisorProcessInfo();
        if (@$info['statename']=='RUNNING')
            return true;
        else
            return false;
    }

    public function getIsAutorestart()
    {
        $info = json_decode($this->supervisor_parameters);
        if (@$info['autorestart']==true)
            return true;
        else
            return false;
    }

    /**
     * @throws \Exception
     */
    public function stopListenerAndTurnOffAutostart()
    {
        $this->updateSupervisorParameters(['autostart'=>0]);
        SupervisorComponent::removeProcess($this->id);
    }

    /**
     * @throws \Exception
     */
    public function startListenerAndTurnOnAutostart()
    {
        $this->updateSupervisorParameters(['autostart'=>1]);
        SupervisorComponent::startProcess($this->id);
    }



    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        SupervisorComponent::removeProcess($this->id);
        return true;
    }






}
