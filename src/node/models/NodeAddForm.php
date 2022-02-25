<?php
namespace lnpay\node\models;

use lnpay\components\HelperComponent;
use lnpay\models\User;
use lnpay\node\components\LndNodeConnector;
use lnpay\node\components\LnMacaroonObject;
use lnpay\models\StatusType;
use lnpay\wallet\models\Wallet;
use yii\base\Exception;
use yii\base\Model;

use Yii;
use yii\helpers\VarDumper;

/**
 * Node Add Form
 */
class NodeAddForm extends Model
{
    public $lndconnect_readonly;
    public $submittedMacaroonObject;
    public $nodeInfo;

    public $node_host;
    public $node_implementation = LnNodeImplementation::LND_IMPLEMENTATION_ID;
    public $node_rest_port = LnNodeImplementation::LND_DEFAULT_REST_PORT;
    public $node_grpc_port = LnNodeImplementation::LND_DEFAULT_GRPC_PORT;
    public $node_ln_port = LnNodeImplementation::LND_DEFAULT_LN_PORT;
    public $node_macaroon;
    public $node_tls_cert;
    public $node_network;
    public $node_uri;
    public $node_id = NULL;
    public $is_custodian;

    public $user_id = NULL;

    public $readyToAdd = 0;
    public $infoVerified = false;

    public $defaultMacaroonPerms = [
        'onchain'   =>    ['read'],
        'offchain'  =>    ['read'],
        'address'   =>    ['read','write'],
        'message'   =>    ['read'],
        'peers'     =>    ['read','write'],
        'info'      =>    ['read'],
        'invoices'  =>    ['read','write'],
        'signer'    =>    ['read']
    ];

    public $readMacaroonPerms = [
        'onchain'   =>    ['read'],
        'offchain'  =>    ['read'],
        'address'   =>    ['read'],
        'message'   =>    ['read'],
        'peers'     =>    ['read'],
        'info'      =>    ['read'],
        'invoices'  =>    ['read'],
        //'signer'    =>    ['read']
    ];

    public function getExpectedMacaroonObject()
    {
        $m = new LnMacaroonObject();
        $m->permissions = $this->readMacaroonPerms;
        return $m;
    }

    /**
     * @return LnNode
     */
    public function getLndMockNodeObject()
    {
        $node = new LnNode();
        $node->ln_node_implementation_id = LnNodeImplementation::LND_IMPLEMENTATION_ID;
        $node->host = $this->node_host;
        $node->rpc_port = $this->node_grpc_port;
        $node->rest_port = $this->node_rest_port;
        $node->tls_cert = $this->node_tls_cert;

        $mac = new LnMacaroonObject($this->node_macaroon);
        $node->baseMacaroonObject = $mac;

        return $node;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['node_host','node_rest_port','node_grpc_port','node_macaroon'],'required'],
            [['node_host','node_macaroon','node_tls_cert'],'string'],
            [['node_rest_port','node_grpc_port','readyToAdd'],'integer'],
            [['is_custodian'],'boolean'],
            [['node_tls_cert'],'verify_tls_cert'],
            ['node_macaroon','verify_macaroon'],
            ['node_host','test_rest_connect'],
            ['node_host','test_rpc_connect'],
            ['node_host','verify_chain'],

            //[['lndconnect_readonly'],'string'],
            //[['lndconnect_readonly'],'lndconnect_verify'],
            //[['lndconnect_readonly'],'verify_read_macaroon'],
            //[['lndconnect_readonly'],'test_connect']
            //['invoice_request', 'either','params' => ['other' => 'num_satoshis']],
        ];
    }

    /**
     * Verify that the TLS cert matches the IP
     * @param $attribute_name
     * @param $params
     */
    public function verify_tls_cert($attribute_name, $params)
    {
        $ip = $this->node_host;

        $rawCert = trim($this->{$attribute_name});

        //check for hex
        if ($r = @hex2bin($this->{$attribute_name})) {
            $rawCert = $r;
        } else {
            $this->{$attribute_name} = bin2hex($this->{$attribute_name});
        }


        $r = openssl_x509_parse($rawCert);
        if (!$r) {
            $this->addError($attribute_name,'Invalid certificate format');
            return false;
        }

        $found = true; //set to FALSE to match hostname to cert
        array_walk_recursive($r, function($item, $key) use ($ip, &$found) {
            if (stripos($item,$ip)!==FALSE) {
                $found = true;
            }
        });

        if (!$found) {
            \LNPay::$app->session->setFlash('invalid_tls',TRUE);
            $this->addError($attribute_name,'Node Host/IP is not listed in TLS cert!');
        }
    }

    /**
     * Verify that the macaroon was baked properly
     * @param $attribute_name
     * @param $params
     */
    public function verify_macaroon($attribute_name, $params)
    {
        $this->submittedMacaroonObject = new LnMacaroonObject($this->node_macaroon);

        return true; //we are not checking anything other than it is a legit macaroon
       /*
        $m = $this->submittedMacaroonObject;

        //Check if permissions are correct
        $diff = $this->expectedMacaroonObject->permissions == $m->permissions;

        $toAddArray = HelperComponent::array_diff_assoc_recursive($this->expectedMacaroonObject->permissions,$m->permissions);
        $toRemoveArray = HelperComponent::array_diff_assoc_recursive($m->permissions,$this->expectedMacaroonObject->permissions);

        $toAdd = LnMacaroonObject::permissionArrayToReadableString($toAddArray,"\n");
        $toRemove = LnMacaroonObject::permissionArrayToReadableString($toRemoveArray,"\n");

        if (!empty($toAdd)) {
            $this->addError($attribute_name,'Macaroon has wrong permissions! Permissions to add: '.$toAdd);
        }

        if (!empty($toRemove)) {
            if ( (stripos($toRemove,'offchain:write')!==FALSE) || (stripos($toRemove,'onchain:write')!==FALSE)) {
                $this->addError($attribute_name,'Macaroon has wrong permissions! Permissions to remove: '.$toRemove);
            }
        }*/
    }

    /**
     * Test if can connect to node via REST
     * @param $attribute_name
     * @param $params
     */
    public function test_rest_connect($attribute_name, $params)
    {
        $mockNode = $this->getLndMockNodeObject();

        $lnd = LndNodeConnector::initConnector($mockNode);
        $r = $lnd->getInfo();
        $decode = @json_decode($r,TRUE);
        if ($decode) {
            $this->nodeInfo = $decode;
        } else {
            $this->addError($attribute_name,'Can\'t connect to node via REST: '.VarDumper::export($r));
        }
    }

    /**
     * Test if can connect to node via RPC
     * @param $attribute_name
     * @param $params
     */
    public function test_rpc_connect($attribute_name, $params)
    {
        $mockNode = $this->getLndMockNodeObject();

        $lnd = LndNodeConnector::initConnector($mockNode,'RPC');
        $r = $lnd->getInfo();
        $decode = @json_decode($r,TRUE);
        if ($decode) {
            //$this->nodeInfo = $decode; //Already set by REST command
        } else {
            $this->addError($attribute_name,'Can\'t connect to node via RPC: '.VarDumper::export($r));
        }
    }

    public function verify_getinfo($attribute_name, $params)
    {
        if ($getinfo = $this->nodeInfo) {
            $this->infoVerified = true;
        } else {
            $this->addError($attribute_name,'Invalid getinfo received from node: '.VarDumper::dumpAsString($this->nodeInfo));
        }
    }

    /**
    public function verify_synced_to_graph($attribute_name, $params)
    {
        if (!$this->nodeInfo['synced_to_graph'])
            $this->addError($attribute_name,'Node must be synced to graph to add!');
    }*/

    public function verify_uri($attribute_name, $params)
    {
        foreach ($this->nodeInfo['uris'] as $uri) {
            $this->node_uri = $uri;
            return;
        }

        $this->addError($attribute_name,'Node does not have any available URIs!');
    }

    public function verify_chain($attribute_name, $params)
    {
        foreach ($this->nodeInfo['chains'] as $chain) {
            if ($chain['chain'] == 'bitcoin') {
                $this->node_network = $chain['network'];
                return;
            }
        }

        $this->addError($attribute_name,'Node not synced to any bitcoin chain!');
    }

    /**
     * Verify if LNDCONNECT is formatted correctly
     * @param $attribute_name
     * @param $params
     * @return bool
     */
    public function lndconnect_verify($attribute_name, $params)
    {
        $r = HelperComponent::parseLndConnectString($this->lndconnect_readonly);

        if (!$r) {
            $this->addError($attribute_name,'Invalid lndconnect string! Check format');
            return false;
        }

        $this->node_host = $r['host'];
        $this->node_rest_port = $r['port'];
        $this->node_grpc_port = LnNodeImplementation::LND_DEFAULT_GRPC_PORT;
        $this->node_macaroon = HelperComponent::base64url_decode($r['macaroon']);
        $this->node_tls_cert = $r['cert'];

    }


    public function either($attribute_name, $params)
    {
        $field1 = $this->getAttributeLabel($attribute_name);
        $field2 = $this->getAttributeLabel($params['other']);
        if (empty($this->$attribute_name) && empty($this->{$params['other']})) {
            $this->addError($attribute_name, \LNPay::t('user', "either {$field1} or {$field2} is required."));
        }
    }

    public function attributeLabels()
    {
        return [
            'invoice_request'=>'Payment Request',
            'readyToAdd'=>'admin.macaroon is encrypted and used to send/receive from this lightning node',
            'is_custodian'=>'Is custodial node for other members of your organization'
        ];
    }












    public function addNode()
    {
        switch ($this->node_implementation) {
            case LnNodeImplementation::LND_IMPLEMENTATION_ID:
            case LnNodeImplementation::LND_SUBNODE:
                return $this->addLndNode();
                break;
        }
    }

    public function addLndNode()
    {
        $nodeInfo = $this->nodeInfo;

        if ($this->node_id) {
            $node = LnNode::findOne($this->node_id);
        } else {
            $node = new LnNode();
        }

        $node->user_id = $this->user_id?:\LNPay::$app->user->id;
        $node->org_id = User::findOne($node->user_id)->org_id;
        $node->alias = $nodeInfo['alias'];
        $node->ln_node_implementation_id = $this->node_implementation;
        $node->default_pubkey = $nodeInfo['identity_pubkey'];
        $node->uri = $node->default_pubkey.'@'.$this->node_host.':'.$this->node_ln_port;
        $node->host = $this->node_host;
        $node->rpc_port = $this->node_grpc_port;
        $node->rest_port = $this->node_rest_port;
        $node->ln_port = $this->node_ln_port;
        $node->tls_cert = $this->node_tls_cert;
        $node->getinfo = $nodeInfo;
        $node->status_type_id = StatusType::LN_NODE_ACTIVE;
        $node->is_custodian = $this->is_custodian;
        $node->network = $this->node_network?:'unknown';

        if ($node->save()) {
            $node->addProfile(['macaroon_hex'=>$this->submittedMacaroonObject->hex,'user_label'=>'admin.macaroon','is_default'=>1]);
            return $node;
        } else {
            throw new Exception(HelperComponent::getFirstErrorFromFailedValidation($node));
        }
    }
}
