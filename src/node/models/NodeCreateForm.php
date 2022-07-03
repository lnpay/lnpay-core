<?php
namespace lnpay\node\models;

use lnpay\components\HelperComponent;
use lnpay\node\components\LndNodeConnector;
use lnpay\node\components\LnMacaroonObject;
use lnpay\models\StatusType;
use lnpay\node\jobs\AddLndSubnodeJob;
use yii\base\Exception;
use yii\base\Model;

use Yii;
use yii\helpers\VarDumper;

/**
 * Node Add Form
 */
class NodeCreateForm extends Model
{
    public $network;
    public $lnd_version;
    public $implementation;
    public $user_label;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['network','lnd_version','user_label','implementation'],'required'],
            [['network','lnd_version','user_label','implementation'],'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'network'=>'Network',
            'lnd_version'=>'Version',
            'implementation'=>'Implementation',
            'user_label' => 'Tags / Labels'
        ];
    }


    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createNode()
    {
        $cluster = LnCluster::getDefaultCluster();
        $result = $cluster->spawnSubnode();

        \LNPay::$app->queue->push(new AddLndSubnodeJob([
            'node_details' => $result
        ]));

        //Create new node in db, placeholder for now
        $node = new LnNode;
        $node->id = $result['node_id'];
        $node->user_id = \LNPay::$app->user->id;
        $node->user_label = $this->user_label;
        $node->rest_port = $result['rest_port'];
        $node->rpc_port = $result['rpc_port'];
        $node->ln_port = $result['internal_ln_port'];
        $node->internal_rpc_port = $result['internal_rpc_port'];
        $node->internal_rest_port = $result['internal_rest_port'];
        $node->internal_ln_port = $result['internal_ln_port'];
        $node->host = $cluster->host;
        $node->tls_cert = bin2hex($result['tls_cert']);
        $node->wallet_password = $result['wallet_password'];
        $node->rest_status_id = StatusType::LN_NODE_REST_INACTIVE;
        $node->rpc_status_id = StatusType::LN_NODE_RPC_INACTIVE;
        $node->status_type_id = StatusType::LN_SUBNODE_PENDING;
        $node->network = $this->network;
        $node->ln_node_implementation_id = LnNodeImplementation::LND_SUBNODE;
        if (!$node->save()) {
            throw new \Exception(HelperComponent::getFirstErrorFromFailedValidation($node));
        }

        unset($result['internal_rpc_port']);
        unset($result['internal_rest_port']);
        unset($result['internal_ln_port']);

        return $result;
    }
}
