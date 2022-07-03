<?php
namespace lnpay\components;


use lnpay\events\ActionEvent;
use lnpay\jobs\IntegrationWebhookRequestJob;
use lnpay\models\action\ActionFeed;
use lnpay\models\action\ActionName;
use lnpay\models\BaseLink;
use lnpay\models\integration\IntegrationWebhook;
use lnpay\models\integration\IntegrationWebhookRequest;
use lnpay\models\LnTx;
use lnpay\wallet\models\Wallet;
use lnpay\wallet\models\WalletType;
use lnpay\node\models\LnNode;
use lnpay\node\models\LnNodeImplementation;
use lnpay\models\StatusType;
use Yii;
use yii\base\BaseObject;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;


class ActionComponent extends Component
{

    public static function baseRegisterAction($event)
    {
        try {
            if (!$event->action_id)
                throw new \Exception('Must have an action ID to use this event');
            $act = ActionName::findOne($event->action_id);

            $action = new ActionFeed;
            $action->user_id = $event->userObject->id;
            $action->action_name_id = $event->action_id;
            $action->actionData = $event->customData;
            if (!$action->save()) {
                \LNPay::error(HelperComponent::getFirstErrorFromFailedValidation($action),__METHOD__);
                throw new \Exception('Could not save action');
            }

            try {
                //If this action should trigger webhook
                if ($action->actionName->is_webhook) {
                    static::processIntegrations($action);
                }

            } catch (\Throwable $e) {
                \LNPay::error('Error process integrations: '.$e.print_r($action->attributes,TRUE),__METHOD__);
            }

            //massage data or custom stuff before analytics
            $logToAnalytics = true;
            $customData = $action->actionData;
            switch ($event->action_id) {
                case ActionName::LND_RPC_GRAPH_TOPOLOGY_UPDATE:
                    $logToAnalytics = false;
                    break;
                case ActionName::USER_CREATED:
                    $customData = [];
                    break;
                default:

            }

            if ($logToAnalytics) {
                AnalyticsComponent::log($event->userObject->id,$act->name,$customData);
            }


            return $action;
        } catch (\Throwable $t) {
            \LNPay::error($t,__METHOD__);
            return false;
        }

    }

    public static function registerAction($actionNameId,$data=[],$userObject=null)
    {
        try {
            $event = new ActionEvent($data);
            if ($userObject)
                $event->userObject = $userObject;

            $event->action_id = $actionNameId;

            \LNPay::info($userObject->id.':'.$event->actionNameObject->name.': Registering action',__METHOD__);
            $actionFeedObject = self::baseRegisterAction($event);

            $callable = ['\lnpay\components\ActionComponent',$event->actionNameObject->name];
            if (is_callable($callable)) {
                \LNPay::info($userObject->id.':'.$event->actionNameObject->name.': Calling post-action function.',__METHOD__);
                call_user_func($callable,$event);
            }


            return $actionFeedObject;
        } catch (\Throwable $t) {
            \LNPay::error($t,__METHOD__);
            return false;
        }
    }



    /**
     * @param $event
     * @return bool
     */

    //wallet actions
    public static function wallet_created($event)
    {

    }
    public static function wallet_send($event) { }
    public static function wallet_receive($event) { }
    public static function wallet_transfer_in($event) { }
    public static function wallet_transfer_out($event) { }


    public static function pw_reset($event) {
        $user = $event->userObject;
        $mailer = MailerComponent::initMailer($user->email,'passwordResetToken-html',compact('user'));
        $mailer->setSubject('Password Reset');
        $mailer->send();
    }

    public static function user_created($event): void
    {
        $user = $event->userObject;
        $user->createDefaultSettings();
        $user->createDefaultWallets();
    }



    //NODE ACTIONS
    public static function user_node_add($event)
    {
        $node = LnNode::findOne(@$event->customData['lnod']['id']);
        switch ($node->ln_node_implementation_id) {
            case LnNodeImplementation::LND_IMPLEMENTATION_ID:
                //Start listeners
                $node->spawnLndRpcSubscribers();
                break;
        }




    }

    public static function user_node_remove($event) {}















    public static function processIntegrations($actionFeedObject)
    {
        $data = $actionFeedObject->actionData;
        $userId = $actionFeedObject->user_id;
        $actionName = $actionFeedObject->actionName;
        $actionData = $actionFeedObject->actionData;

        $baseWhereClause = ['user_id'=>$userId,'status_type_id'=>StatusType::WEBHOOK_ACTIVE];

        //legacy hooks
        $legacyActionHook = [];
        switch ($actionName->type) {
            case ActionName::TYPE_WALLET:
                $fId = Wallet::findOne(@$data['wallet']['id']);
                if ($fId)
                    $specObjectWhere = ['wallet_id'=>$fId];
                break;
            default:
                $specObjectWhere = [];
        }
        if (!empty($specObjectWhere))
            $legacyActionHook = IntegrationWebhook::find()->where($baseWhereClause)->andWhere($specObjectWhere)->all();

        //If user is subscribed to just this one event
        $specificActionWhere = ['IS NOT',"JSON_SEARCH(action_name_id, 'one', '{$actionName->name}')",NULL];
        $specificActionHook = IntegrationWebhook::find()->where($baseWhereClause)->andWhere($specificActionWhere)->all();

        //If user is subscribed to all events
        $catchAllAction = IntegrationWebhook::DEFAULT_ALL;
        $catchAllActionWhere = ['IS NOT',"JSON_SEARCH(action_name_id, 'one', '{$catchAllAction}')",NULL];
        $catchAllHooks = IntegrationWebhook::find()->where($baseWhereClause)->andWhere($catchAllActionWhere)->all();

        //Admin catch all for testing
        $adminHookWhere = ['IS NOT',"JSON_SEARCH(action_name_id, 'one', 'admin_all')",NULL];
        $adminHooks = IntegrationWebhook::find()->where($adminHookWhere)->andWhere(['status_type_id'=>StatusType::WEBHOOK_ACTIVE])->all();


        //NEW METHOD BASED ON ARRAY OF ACTION_NAME_IDs
        $hooks = ArrayHelper::merge($specificActionHook,$legacyActionHook,$catchAllHooks,$adminHooks);
        \LNPay::info($actionFeedObject->id.':'.$actionFeedObject->actionName->name.': Sending webhooks count ->'.count($hooks),__METHOD__);

        foreach ($hooks as $IW) {
            //Construct payload
            $iwhr = IntegrationWebhookRequest::prepareRequest($IW,$actionFeedObject);

            /*
            \LNPay::$app->queue->priority(150)->push(new IntegrationWebhookRequestJob([
                'iwhr_id' => $iwhr->id
            ]));
            */

            $job = new IntegrationWebhookRequestJob([
                'iwhr_id' => $iwhr->id
            ]);
            $exec = $job->execute(\LNPay::$app->queue);
        }
    }

    /**
     * @param IntegrationWebhookRequest $iwr
     */
    public static function webhookRequest(IntegrationWebhookRequest $iwhr)
    {
        try {
            $IW = $iwhr->integrationWebhook;
            $actionFeedObject = $iwhr->actionFeed;

            $headers = [
                'Content-Type' => [$IW->content_type],
                'X-LNPay-Event' => [$actionFeedObject->actionName->name],
                'X-LNPay-HookId' => [$iwhr->external_hash],
                'User-Agent' => ['LNPay-HookBot']
            ];

            if ($s = $IW->secret) {
                $headers['X-LNPay-Signature'] = [hash_hmac('sha256',$iwhr->request_payload,$s)];
            }

            $client = new \GuzzleHttp\Client([
                'http_errors'=>false,
                'headers' => $headers
            ]);

            $response = $client->request($IW->http_method, $IW->endpoint_url, [
                'body' => $iwhr->request_payload
            ]);

            //This is a hack for now to include headers to show to user
            $headers = ArrayHelper::merge([$IW->http_method=>[$IW->endpoint_url]],$headers);
            $iwhr->request_payload = HelperComponent::parseHeaderArrayToString($headers)."\n".json_encode(json_decode($iwhr->request_payload),JSON_PRETTY_PRINT);
            $iwhr->save();

            return $iwhr->processResponse($response);

        } catch (\Throwable $e) {
            \LNPay::error($e->getMessage(),__METHOD__);
            $iwhr->response_status_code = -1;
            $iwhr->response_body = $e->getMessage();
            $iwhr->save();
        }
    }

    /**
     * Send initial ping on new webhook create
     * @param $IW
     */
    public static function webhookPing($IW)
    {
        //@TODO: Implement this
    }

    public static function getAvailableTestActionObjects()
    {
        $array = [
            ActionName::findOne(ActionName::WALLET_CREATED),
            ActionName::findOne(ActionName::WALLET_SEND),
            ActionName::findOne(ActionName::WALLET_RECEIVE),
            ActionName::findOne(ActionName::WALLET_TRANSFER_IN),
            ActionName::findOne(ActionName::WALLET_TRANSFER_OUT),
        ];
        return $array;
    }

    public static function getTestWebhookActionFeedObject(int $actionId)
    {
        if (YII_ENV_PROD) {
            $array = [
                ActionName::WALLET_CREATED => ActionFeed::findOne(90165),
                ActionName::WALLET_SEND => ActionFeed::findOne(90171),
                ActionName::WALLET_RECEIVE => ActionFeed::findOne(91534),
                ActionName::WALLET_TRANSFER_IN => ActionFeed::findOne(90332),
                ActionName::WALLET_TRANSFER_OUT => ActionFeed::findOne(90331),
            ];
        } else {
            $a = ActionFeed::find()->orderBy('id DESC')->one();
            $array = [
                ActionName::WALLET_CREATED => $a,
                ActionName::WALLET_SEND => $a,
                ActionName::WALLET_RECEIVE => $a,
                ActionName::WALLET_TRANSFER_IN => $a,
                ActionName::WALLET_TRANSFER_OUT => $a,
            ];
        }


        if ($actionId) {
            return $array[$actionId];
        } else {
            return $array;
        }
    }
}

