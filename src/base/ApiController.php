<?php

namespace lnpay\base;

use lnpay\behaviors\UserAccessKeyBehavior;
use lnpay\models\User;
use lnpay\wallet\models\Wallet;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpHeaderAuth;
use yii\filters\auth\QueryParamAuth;
use yii\rest\ActiveController;

use Yii;
use yii\web\UnauthorizedHttpException;

class ApiController extends ActiveController
{
    /**
     * @var array of functions that only the sak_ can access
     */
    public $sakOnlyArray=[];

    public $enableCsrfValidation = false;
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class
        ];

        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                HttpHeaderAuth::class,
                QueryParamAuth::class,
                HttpBasicAuth::class
            ],
            'except'=>['lnurl-process','options','lightning-address']
        ];
        $behaviors['contentNegotiator'] = [
            'class' => 'yii\filters\ContentNegotiator',
            'formats' => [
                'application/json' => \yii\web\Response::FORMAT_JSON,
            ],
        ];

        $behaviors['rateLimiter']['enableRateLimitHeaders'] = false;
        return $behaviors;
    }

    public function init()
    {
        parent::init();
        \LNPay::$app->user->enableSession = false;
        set_time_limit(10);
    }

    public function checkAdminAccess()
    {
        if (\LNPay::$app->user->identity->status >= User::STATUS_API_ADMIN)
            return true;
        else
            throw new UnauthorizedHttpException('You do not have permission to do this!');
    }

    public function getUser()
    {
        return User::findOne(\LNPay::$app->user->id);
    }

    public function getIsAsync()
    {
        $headers = \LNPay::$app->request->getHeaders();
        $async = $headers->get('X-LNPAY-ASYNC',NULL);
        if ($async) {
            return true;
        } else {
            return false;
        }

    }

    public function beforeAction($event)
    {
        if (parent::beforeAction($event)) {

            //We can check for perms or other things on the current session key

            $apiKey = @\LNPay::$app->user->identity->sessionApiKey;

            $function = \LNPay::$app->controller->id.'/'.\LNPay::$app->controller->action->id;

            if (UserAccessKeyBehavior::checkKeyAccess(UserAccessKeyBehavior::ROLE_PUBLIC_API_KEY,$apiKey)) {
                if (in_array($function,$this->sakOnlyArray)) {
                    throw new UnauthorizedHttpException('This resource can only be access with secret access key.');
                }
            }

            if (UserAccessKeyBehavior::checkKeyAccess(UserAccessKeyBehavior::ROLE_KEY_SUSPENDED,$apiKey)) {
                throw new UnauthorizedHttpException('Something went wrong, please reach out.');
            }
            return true;
        }

    }


    public function findByKey($access_key)
    {
        /**
         * @var lnpay\wallet\models\Wallet
         */
        $modelClass = $this->modelClass;

        $apiKey = @\LNPay::$app->user->identity->sessionApiKey;

        //If SAK is used, all wallet keys are valid
        if (UserAccessKeyBehavior::checkKeyAccess(UserAccessKeyBehavior::ROLE_SECRET_API_KEY,$apiKey)) {
            $wallet = Wallet::findById($access_key) ?? Wallet::findByKey($access_key) ?? NULL;
            if ($wallet) {
                if ($wallet->user_id == \LNPay::$app->user->id) { //make sure this is the right user
                    return $wallet;
                }
            }

        }
        //public access key
        else if (UserAccessKeyBehavior::checkKeyAccess(UserAccessKeyBehavior::ROLE_PUBLIC_API_KEY,@\LNPay::$app->user->identity->sessionApiKey)) {
            $wallet = Wallet::findByKey($access_key);
            if ($wallet) {
                if (\LNPay::$app->user->id == $wallet->user_id) {
                    return $wallet;
                }
            }
        } else { //publicly available with no key needed
            if (in_array($this->action->id,['lnurl-process','lightning-address'])) { //if lnurl which grants public access based on key
                $wallet = Wallet::findByKey($access_key);
                if ($wallet) {
                    return $wallet;
                }
            }
        }

        if (UserAccessKeyBehavior::checkKeyAccess(UserAccessKeyBehavior::ROLE_PUBLIC_API_KEY,@\LNPay::$app->user->identity->sessionApiKey)) {
            throw new UnauthorizedHttpException('Invalid Wallet Access Key. Keys prefixed with waka_, waki_, wakr, waklw are valid when using pak_');
        }
        throw new UnauthorizedHttpException('Wallet not found: '.$access_key);
    }

    public function checkAccessKey($item,$access_key=NULL)
    {
        if (!$access_key) //assuming it's a wallet access key if it's in the URL
            $access_key = \LNPay::$app->request->getQueryParam('access_key');

        if (UserAccessKeyBehavior::checkKeyAccess(UserAccessKeyBehavior::ROLE_SECRET_API_KEY,@\LNPay::$app->user->identity->sessionApiKey)) {
            return true;
        } else if (UserAccessKeyBehavior::checkKeyAccess(UserAccessKeyBehavior::ROLE_KEY_SUSPENDED,$access_key)) {
            throw new UnauthorizedHttpException('Key has been suspended');
        } else if (UserAccessKeyBehavior::checkKeyAccess($item,$access_key))
            return true;
        else
            throw new UnauthorizedHttpException(UserAccessKeyBehavior::getAccessKeyPrefix($access_key).' access key provided does not permission to do this: '.$item);

    }

}
