<?php

namespace app\controllers\v1;

use app\behaviors\UserAccessKeyBehavior;
use app\models\User;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpHeaderAuth;
use yii\filters\auth\QueryParamAuth;
use yii\rest\ActiveController;

use Yii;
use yii\web\UnauthorizedHttpException;

class BaseApiController extends ActiveController
{
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
            'except'=>['lnurl-process','lnurl-process-public','options']
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
        \Yii::$app->user->enableSession = false;
        set_time_limit(10);
    }

    public function checkAdminAccess()
    {
        if (Yii::$app->user->identity->status >= User::STATUS_API_ADMIN)
            return true;
        else
            throw new UnauthorizedHttpException('You do not have permission to do this!');
    }

    public function getUser()
    {
        return User::findOne(Yii::$app->user->id);
    }

    public function getIsAsync()
    {
        $headers = Yii::$app->request->getHeaders();
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

            $apiKey = @Yii::$app->user->identity->sessionApiKey;

            $function = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;

            $sakOnlyArray = [
                'v1/wallet-transaction/view-all',
                'v1/wallet/view-all',
            ];

            if (UserAccessKeyBehavior::checkKeyAccess(UserAccessKeyBehavior::ROLE_PUBLIC_API_KEY,$apiKey)) {
                if (in_array($function,$sakOnlyArray)) {
                    throw new UnauthorizedHttpException('This resource can only be access with secret access key.');
                }
            }

            if (UserAccessKeyBehavior::checkKeyAccess(UserAccessKeyBehavior::ROLE_KEY_SUSPENDED,$apiKey)) {
                throw new UnauthorizedHttpException('Something went wrong, please reach out.');
            }
            return true;
        }

    }

}
