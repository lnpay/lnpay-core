<?php

namespace app\modules\node\models;

use app\behaviors\JsonDataBehavior;
use app\components\HelperComponent;
use app\components\SupervisorComponent;
use app\jobs\SupervisorUpdateLndRpcConfigFileJob;
use app\jobs\SupervisorWriteLndRpcConfigFileJob;
use app\models\action\ActionName;
use app\models\StatusType;
use app\models\User;
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
            case static::LND_RPC_SUBSCRIBE_INVOICES:
                $overrides = [
                    'autostart'=>true
                ];
                break;
            case static::LND_RPC_SUBSCRIBE_TRANSACTIONS:
                $overrides = [
                    'autostart'=>true
                ];
                break;
            default:
                $overrides = [];
        }

        return ArrayHelper::merge([
            'command' => getenv('PHP_BIN_PATH').' '.getenv('SUPERVISOR_SERVER_APP_PATH').'yii rpc-listener/lnd-subscribe '.$nodeObject->id.' '.$method,
            'autostart'=>false,
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
            throw new \Exception(HelperComponent::getErrorStringFromInvalidModel($listener));
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

        Yii::$app->queue->push(new SupervisorWriteLndRpcConfigFileJob([
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
        Yii::$app->queue->push(new SupervisorUpdateLndRpcConfigFileJob([
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
