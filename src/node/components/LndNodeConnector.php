<?php
namespace lnpay\node\components;


use lnpay\models\action\ActionName;
use lnpay\node\exceptions\UnableToBakeMacaroonException;
use lnpay\node\exceptions\UnableToCreateInvoiceException;
use lnpay\node\exceptions\UnableToDecodeInvoiceException;
use lnpay\node\exceptions\UnableToGetChannelBalanceException;
use lnpay\node\exceptions\UnableToGetWalletBalanceException;
use lnpay\node\exceptions\UnableToListChannelsException;
use lnpay\node\exceptions\UnableToLookupInvoiceException;
use lnpay\node\exceptions\UnableToPayInvoiceException;
use lnpay\node\exceptions\UnableToQueryRoutesException;
use lnpay\node\exceptions\UnableToSendKeysendException;
use lnpay\node\models\LnNode;
use Lnrpc\BakeMacaroonRequest;
use Lnrpc\ChanInfoRequest;
use Lnrpc\ChannelBalanceRequest;
use Lnrpc\DeleteAllPaymentsRequest;
use Lnrpc\GenSeedRequest;
use Lnrpc\GetInfoRequest;
use Lnrpc\Invoice;
use Lnrpc\ListChannelsRequest;
use Lnrpc\NewAddressRequest;
use Lnrpc\NodeInfoRequest;
use Lnrpc\PaymentHash;
use Lnrpc\PayReqString;
use Lnrpc\QueryRoutesRequest;
use Lnrpc\SendRequest;
use Lnrpc\WalletBalanceRequest;
use Routerrpc\SendPaymentRequest;
use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;


class LndNodeConnector extends LnBaseNodeClass implements LnBaseNodeInterface
{
    private $_nodeObject = NULL;
    private $_webService = 'REST'; //REST or RPC

    const SERVICE_LIGHTNING = 'lightning';
    const SERVICE_ROUTER = 'router';
    const SERVICE_WALLET_UNLOCKER = 'wallet_unlocker';
    const SERVICE_WALLET_KIT = 'wallet_kit';

    const KEYSEND_TLV_KEY = 5482373484;
    const KEYSEND_LNPAY_KEY = 696969;
    const KEYSEND_PODCAST_KEY = 112111100; //ascii "pod"
    const KEYSEND_SPADS_KEY = 55555555; //ascii "pod"
    const KEYSEND_PODCAST_FEESPLIT_KEY = 112111100102 ; //ascii "podf"
    const KEYSEND_PODCAST_KEY_DATA = 7629169; //satoshi.stream stuff

    //these keys will be checked to see if they contain a valid wallet ID
    public static function getThirdPartyTlvWalletIdKeys()
    {
        return [
            self::KEYSEND_LNPAY_KEY,
            self::KEYSEND_PODCAST_KEY,
            self::KEYSEND_PODCAST_FEESPLIT_KEY
        ];
    }

    //these keys will be checked for data and stored with the transaction
    public static function getThirdPartyTlvDataKeys()
    {
        return [self::KEYSEND_PODCAST_KEY_DATA,self::KEYSEND_SPADS_KEY];
    }

    /**
     * @param LnNode $lnNodeObject
     * @return LndNodeConnector
     */
    public static function initConnector(LnNode $lnNodeObject, $webService = 'REST')
    {
        $lndConnector = new static();
        $lndConnector->endpoint = 'https://'.$lnNodeObject->host.':'.$lnNodeObject->rest_port.'/v1/';
        $lndConnector->macaroonHex = $lnNodeObject->baseMacaroonObject->hex;
        $lndConnector->tlsCert = $lnNodeObject->tls_cert;
        $lndConnector->webService = $webService;
        $lndConnector->node = $lnNodeObject;

        return $lndConnector;
    }

    /**
     * @param LnNode $lnNodeObject
     * @return \Lnrpc\LightningClient|\Routerrpc\RouterClient
     */
    public static function initConnectorRpc(LnNode $lnNodeObject, $service='lightning')
    {
        $cert = hex2bin($lnNodeObject->tls_cert);
        $macaroon = $lnNodeObject->baseMacaroonObject->hex;
        $callback = function ($metadata) use ($macaroon) {
            return ['macaroon' => [$macaroon]];
        };

        $credentials = \Grpc\ChannelCredentials::createSsl($cert);

        switch ($service) {
            case self::SERVICE_LIGHTNING:
                $rpc = new \Lnrpc\LightningClient($lnNodeObject->host.':'.$lnNodeObject->rpc_port,['credentials'=>$credentials,'update_metadata'=>$callback]);
                break;
            case self::SERVICE_ROUTER:
                $rpc = new \Routerrpc\RouterClient($lnNodeObject->host.':'.$lnNodeObject->rpc_port,['credentials'=>$credentials,'update_metadata'=>$callback]);
                break;
            case self::SERVICE_WALLET_UNLOCKER:
                $rpc = new \Lnrpc\WalletUnlockerClient($lnNodeObject->host.':'.$lnNodeObject->rpc_port,['credentials'=>$credentials,'update_metadata'=>$callback]);
                break;
            case self::SERVICE_WALLET_KIT:
                $rpc = new \Walletrpc\WalletKitClient($lnNodeObject->host.':'.$lnNodeObject->rpc_port,['credentials'=>$credentials,'update_metadata'=>$callback]);
                break;

        }


        return $rpc;
    }


    /**
     * @param $nodeObjectOrId
     * @throws \Exception
     */
    public function setNode($nodeObjectOrId)
    {
        if ($nodeObjectOrId instanceof LnNode)
            $this->_nodeObject = $nodeObjectOrId;
        else
            $this->_nodeObject = LnNode::findOne($nodeObjectOrId);

        if (!$this->_nodeObject)
            throw new \Exception('Invalid Node ID provided to connector:'.$nodeObjectOrId);
    }

    public function setWebService($webService)
    {
        $this->_webService = $webService;
    }

    public function createInvoice($invoiceOptions)
    {
        $r = $this->lnd_rpc_request('AddInvoice',$invoiceOptions);
        $arr = @json_decode($r,TRUE);

        if (!$arr)
            throw new UnableToCreateInvoiceException($r);

        //append the node ID to the array just so we know
        $arr['ln_node_id'] = $this->_nodeObject->id;

        //GRPC backwards compatible
        $arr['payment_request'] = $arr['paymentRequest'];
        $arr['r_hash'] = $arr['rHash'];

        return $arr;

    }
    public function checkInvoice($request) {}

    /**
     * @param $request
     * @return mixed
     * @throws \Exception
     */
    public function decodeInvoice($request)
    {
        $r = $this->lnd_rpc_request('DecodePayReq',['pay_req'=>$request]);
        $arr = @json_decode($r,TRUE);

        if (!$arr)
            throw new UnableToDecodeInvoiceException($r);

        //GRPC backwards compatible
        $arr['payment_hash'] = $arr['paymentHash']; unset($arr['paymentHash']);
        $arr['num_satoshis'] = $arr['numSatoshis']; unset($arr['numSatoshis']);
        $arr['cltv_expiry'] = $arr['cltvExpiry']; unset($arr['cltvExpiry']);

        return $arr;
    }

    public function lookupInvoice($payment_hash)
    {
        $r = $this->lnd_rpc_request('LookupInvoice',['r_hash_str'=>$payment_hash]);
        $arr = @json_decode($r,TRUE);

        if (!$arr)
            throw new UnableToLookupInvoiceException($r);

        return $arr;
    }

    public function queryRoutes($bodyArray)
    {
        $r = $this->lnd_rpc_request('QueryRoutes',$bodyArray);
        $arr = @json_decode($r,TRUE);

        if (!$arr)
            throw new UnableToQueryRoutesException($r);

        return $arr;
    }

    public function walletBalance($bodyArray=[])
    {
        $r = $this->lnd_rpc_request('WalletBalance',$bodyArray);
        $arr = @json_decode($r,TRUE);

        if (!$arr)
            throw new UnableToGetWalletBalanceException($r);

        return $arr;
    }

    public function channelBalance($bodyArray=[])
    {
        $r = $this->lnd_rpc_request('ChannelBalance',$bodyArray);
        $arr = @json_decode($r,TRUE);

        if (!$arr)
            throw new UnableToGetChannelBalanceException($r);

        return $arr;
    }

    public function newAddress($bodyArray=[])
    {
        $r = $this->lnd_rpc_request('NewAddress',$bodyArray);
        $arr = @json_decode($r,TRUE);

        if (!$arr)
            throw new UnableToGetWalletBalanceException($r);

        return $arr;
    }

    /**
     * @param $request
     * @return array|mixed|string
     * @throws UnableToPayInvoiceException
     * @throws UnableToSendKeysendException
     */
    public function payInvoice($request,$options=[])
    {
        $data = ArrayHelper::merge([
            'payment_request'=>$request,
            'timeout_seconds'=>10,
            'no_inflight_updates'=>1,
            'allow_self_payment'=>1
        ],$options);

        $arr = $this->lnd_rpc_request('SendPaymentV2',$data);

        if (!is_array($arr)) //the request to lnd is malformed in some way
            throw new UnableToPayInvoiceException($arr);

        if (@$arr['status'] != 'SUCCEEDED') { //the payment legit failed for lightning reason
            $this->_nodeObject->user->registerAction(ActionName::LN_NODE_INVOICE_PAYMENT_FAILURE,['lnod'=>$this->_nodeObject->toArray(),'request_parameters'=>$data,'failureReason'=>@$arr['failureReason']]);
            throw new UnableToPayInvoiceException(@$arr['failureReason']);
        }

        $arr['payment_preimage'] = $arr['paymentPreimage'];
        $arr['payment_hash'] = $arr['paymentHash'];


        return $arr;
    }

    /**
     * @param $dest
     * @param $num_satoshis
     * @param array $dest_custom_records
     * @param array $options
     * @return mixed|string
     * @throws UnableToSendKeysendException
     */
    public function keysend($dest,$num_satoshis,$dest_custom_records=[],$options=[])
    {
        $preimage = random_bytes(32);
        $data = ArrayHelper::merge([
            'dest'=>hex2bin($dest),
            'timeout_seconds'=>10,
            'amt'=>$num_satoshis,
            'no_inflight_updates'=>1,
            'allow_self_payment'=>1,
            'payment_hash'=>hex2bin(hash('sha256',$preimage)),
            'dest_custom_records'=> ArrayHelper::merge($dest_custom_records,[
                self::KEYSEND_TLV_KEY => $preimage
            ])
        ],$options);

        $arr = $this->lnd_rpc_request('SendPaymentV2',$data);

        if (!is_array($arr)) //the request to LND is malformed somehow
            throw new UnableToSendKeysendException($arr);

        if (@$arr['status'] != 'SUCCEEDED') { //legit keysend failure
            //need to clean up data a bit for db
            $data['dest'] = bin2hex($data['dest']);
            $data['payment_hash'] = bin2hex($data['payment_hash']);
            unset($data['dest_custom_records'][self::KEYSEND_TLV_KEY]);


            $this->_nodeObject->user->registerAction(ActionName::LN_NODE_SPONTANEOUS_SEND_FAILURE,['lnod'=>$this->_nodeObject->toArray(),'request_parameters'=>$data,'failureReason'=>@$arr['failureReason']]);
            throw new UnableToSendKeysendException(@$arr['failureReason']);
        }

        return $arr;
    }


    public function bakeMacaroon($perms)
    {
        $r = $this->lnd_rpc_request('BakeMacaroon',$perms);

        $arr = @json_decode($r,TRUE);

        if (!array_key_exists('macaroon',$arr))
            throw new UnableToBakeMacaroonException($arr);

        return $arr['macaroon'];
    }

    public function deleteAllPayments()
    {
        $r = $this->lnd_rpc_request('DeleteAllPayments');

        return true;
    }

    public function listChannels() {
        $r = $this->lnd_rpc_request('ListChannels');
        $arr = @json_decode($r,TRUE);

        if (!$arr)
            throw new UnableToListChannelsException($r);

        return $arr;
    }

    public function nodeInfo($data) {
        try {
            return $this->lnd_rpc_request('NodeInfo',$data);
        } catch (\Throwable $t) {
            \LNPay::error($t->getMessage(),__METHOD__);
        }
    }

    public function chanInfo($data) {
        try {
            return $this->lnd_rpc_request('ChanInfo',$data);
        } catch (\Throwable $t) {
            \LNPay::error($t->getMessage(),__METHOD__);
        }
    }

    public function getInfo() {
        try {
            switch ($this->_webService) {
                case 'RPC':
                    return $this->lnd_rpc_request('GetInfo');
                    break;
                case 'REST':
                    return $this->lnd_rest_request('getinfo');
                    break;
            }
        } catch (\Throwable $t) {
            \LNPay::error($t->getMessage(),__METHOD__);
        }
    }

    protected function lnd_rpc_request($method, $bodyArray=[])
    {
        \LNPay::info('Attempting RPC:'.$method.' ('.$this->_nodeObject->id.') : '.VarDumper::dumpAsString($bodyArray),__METHOD__);
        //\LNPay::info($this->_nodeObject->host.':'.$this->_nodeObject->rpc_port);
        try {
            switch ($method) {
                case 'ChanInfo':
                    $rpcConnector = static::initConnectorRpc($this->_nodeObject);
                    $r = new ChanInfoRequest($bodyArray);
                    $resp = $rpcConnector->GetChanInfo($r)->wait();
                    break;
                case 'NodeInfo':
                    $rpcConnector = static::initConnectorRpc($this->_nodeObject);
                    $r = new NodeInfoRequest($bodyArray);
                    $resp = $rpcConnector->GetNodeInfo($r)->wait();
                    break;
                case 'ListChannels':
                    $rpcConnector = static::initConnectorRpc($this->_nodeObject);
                    $r = new ListChannelsRequest();
                    $resp = $rpcConnector->ListChannels($r)->wait();
                    break;
                case 'GetInfo':
                    $rpcConnector = static::initConnectorRpc($this->_nodeObject);
                    $r = new GetInfoRequest();
                    $resp = $rpcConnector->GetInfo($r)->wait();
                    break;
                case 'AddInvoice':
                    $rpcConnector = static::initConnectorRpc($this->_nodeObject);
                    $r = new Invoice($bodyArray);
                    $resp = $rpcConnector->AddInvoice($r)->wait();
                    break;
                case 'SendPaymentSync':
                    $rpcConnector = static::initConnectorRpc($this->_nodeObject);
                    $r = new SendRequest($bodyArray);
                    $resp = $rpcConnector->SendPaymentSync($r)->wait();
                    break;
                case 'DecodePayReq':
                    $rpcConnector = static::initConnectorRpc($this->_nodeObject);
                    $r = new PayReqString($bodyArray);
                    $resp = $rpcConnector->DecodePayReq($r)->wait();
                    break;
                case 'LookupInvoice':
                    $rpcConnector = static::initConnectorRpc($this->_nodeObject);
                    $r = new PaymentHash($bodyArray);
                    $resp = $rpcConnector->LookupInvoice($r)->wait();
                    break;
                case 'SendPaymentV2':
                    $rpcConnector = static::initConnectorRpc($this->_nodeObject,self::SERVICE_ROUTER);
                    $r = new SendPaymentRequest($bodyArray);
                    $resp = $rpcConnector->SendPaymentV2($r);
                    foreach ($resp->responses() as $rp) {
                        $json = $rp->serializeToJsonString();
                        \LNPay::info('('.$method.') Response:'.VarDumper::export($json),__METHOD__);
                        return json_decode($json,TRUE);
                    }
                    //if error
                    return $resp->getStatus()->details;
                    break;
                case 'BakeMacaroon':
                    $rpcConnector = static::initConnectorRpc($this->_nodeObject);
                    $r = new BakeMacaroonRequest();
                    $r->setPermissions($bodyArray);
                    $resp = $rpcConnector->BakeMacaroon($r)->wait();
                    break;
                case 'GenSeed':
                    $rpcConnector = static::initConnectorRpc($this->_nodeObject);
                    $r = new GenSeedRequest();
                    $resp = $rpcConnector->GenSeed($r)->wait();
                    break;
                case 'DeleteAllPayments':
                    $rpcConnector = static::initConnectorRpc($this->_nodeObject);
                    $r = new DeleteAllPaymentsRequest();
                    $resp = $rpcConnector->DeleteAllPayments($r)->wait();
                    break;
                case 'QueryRoutes':
                    $rpcConnector = static::initConnectorRpc($this->_nodeObject);
                    $r = new QueryRoutesRequest($bodyArray);
                    $resp = $rpcConnector->QueryRoutes($r)->wait();
                    break;
                case 'WalletBalance':
                    $rpcConnector = static::initConnectorRpc($this->_nodeObject, self::SERVICE_LIGHTNING);
                    $r = new WalletBalanceRequest($bodyArray);
                    $resp = $rpcConnector->WalletBalance($r)->wait();
                    break;
                case 'ChannelBalance':
                    $rpcConnector = static::initConnectorRpc($this->_nodeObject, self::SERVICE_LIGHTNING);
                    $r = new ChannelBalanceRequest($bodyArray);
                    $resp = $rpcConnector->ChannelBalance($r)->wait();
                    break;
                case 'NewAddress':
                    $rpcConnector = static::initConnectorRpc($this->_nodeObject, self::SERVICE_LIGHTNING);
                    $r = new NewAddressRequest($bodyArray);
                    $resp = $rpcConnector->NewAddress($r)->wait();
                    break;
            }


            if ($resp[0]) {
                $json = @$resp[0]->serializeToJsonString();
                \LNPay::info('('.$method.') Response:'.VarDumper::export($json),__METHOD__);
                return $json;
            }
            else {
                $error = $resp[1]->details;
                \LNPay::info('('.$method.') Response:'.VarDumper::export($error),__METHOD__);
                return $error;
            }

        } catch (\Throwable $t) {
            return $t->getMessage();
        }
    }

    /**
     * @param $path
     * @param array $bodyArray
     * @return string|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function lnd_rest_request($path,$bodyArray = [])
    {
        \LNPay::info('Attempting REST:'.$path.' ('.$this->_nodeObject->id.'): '.VarDumper::dumpAsString($bodyArray),__METHOD__);
        //Saving TLS cert to disk so we can use it. not sure how else to do this
        $this->saveTlsCertToDisk();

        $requestUrl = $this->endpoint . $path;
        $headers = ['Grpc-Metadata-macaroon'=>$this->macaroonHex];
        $client = new \GuzzleHttp\Client([
            'curl'=> [
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0
            ],
            'http_errors'=>true,
            'headers' => $headers,
            'debug'=>false,
            'connect_timeout'=>2
        ]);
        $r = null;
        if (empty($bodyArray)) {
            $response = $client->request('GET', $requestUrl, [
                'verify'=>$this->tlsCertFilename,
            ]);
            $r = $response->getBody()->getContents();
        } else {
            $response = $client->request('POST', $requestUrl, [
                'verify'=>$this->tlsCertFilename,
                'json' => $bodyArray
            ]);
            $r = $response->getBody()->getContents();
        }

        \LNPay::info('REST ('.$path.') Response:'.VarDumper::export($r),__METHOD__);

        //Delete the TLS cert we saved earlier
        $this->deleteTlsCertFromDisk();

        return $r;
    }

    protected function saveTlsCertToDisk()
    {
        file_put_contents($this->getTlsCertFilename(),hex2bin($this->tlsCert));
    }

    protected function deleteTlsCertFromDisk()
    {
        unlink($this->getTlsCertFilename());
    }

    public function getTlsCertFilename()
    {
        return \LNPay::getAlias('@root').'/runtime/node_tls/'.substr(md5($this->tlsCert),0,12).'.cert';
    }




















    /*************************************************************
     * RPC SECTION
     *************************************************************/

    public function rpcSubscribeInvoices($callback)
    {
        $rpcConnector = static::initConnectorRpc($this->_nodeObject);
        $sub = new \Lnrpc\InvoiceSubscription();
        $result = $rpcConnector->SubscribeInvoices($sub);

        foreach ($result->responses() as $response) {
            $callback($response);
        }
    }

    public function rpcSubscribePeerEvents($callback)
    {
        $rpcConnector = static::initConnectorRpc($this->_nodeObject);
        $sub = new \Lnrpc\PeerEventSubscription();
        $result = $rpcConnector->SubscribePeerEvents($sub);

        foreach ($result->responses() as $response) {
            $callback($response);
        }
    }

    public function rpcSubscribeTransactions($callback)
    {
        $rpcConnector = static::initConnectorRpc($this->_nodeObject);
        $sub = new \Lnrpc\GetTransactionsRequest();
        $result = $rpcConnector->SubscribeTransactions($sub);

        foreach ($result->responses() as $response) {
            $callback($response);
        }
    }

    public function rpcSubscribeChannelEvents($callback)
    {
        $rpcConnector = static::initConnectorRpc($this->_nodeObject);
        $sub = new \Lnrpc\ChannelEventSubscription();
        $result = $rpcConnector->SubscribeChannelEvents($sub);

        foreach ($result->responses() as $response) {
            $callback($response);
        }
    }

    public function rpcSubscribeChannelGraph($callback)
    {
        $rpcConnector = static::initConnectorRpc($this->_nodeObject);
        $sub = new \Lnrpc\GraphTopologySubscription();
        $result = $rpcConnector->SubscribeChannelGraph($sub);

        foreach ($result->responses() as $response) {
            $callback($response);
        }
    }

    public function rpcSubscribeChannelBackups($callback)
    {
        $rpcConnector = static::initConnectorRpc($this->_nodeObject);
        $sub = new \Lnrpc\ChannelBackupSubscription();
        $result = $rpcConnector->SubscribeChannelBackups($sub);

        foreach ($result->responses() as $response) {
            $callback($response);
        }
    }

    public function rpcSubscribeHtlcEvents($callback)
    {
        $rpcConnector = static::initConnectorRpc($this->_nodeObject,self::SERVICE_ROUTER);
        $sub = new \Routerrpc\SubscribeHtlcEventsRequest();
        $result = $rpcConnector->SubscribeHtlcEvents($sub);

        foreach ($result->responses() as $response) {
            $callback($response);
        }
    }

    /*************************************************************
     * END RPC SECTION
     *************************************************************/
}