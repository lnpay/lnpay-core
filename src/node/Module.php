<?php

namespace lnpay\node;

use yii\base\BootstrapInterface;
use yii\filters\AccessControl;
use yii\web\Application as WebApplication;
use yii\web\GroupUrlRule;

class Module extends \yii\base\Module implements BootstrapInterface
{
    public $controllerNamespace = 'lnpay\node\controllers';
    public $sidebarView = '@app/node/views/_nav-node-index.php';
    public $homeUrl = '/node/dashboard/index';

    public function init()
    {
        parent::init();
        // custom initialization code goes here
    }

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        //Admin rules
        $app->urlManager->addRules([[
            'class' => GroupUrlRule::class,
            'prefix' => $this->id,
            'rules' => [
                //Admin dashboard stuff
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action\w+>' => '<controller>/<action>',
                '<controller:\w+>/<action\w+>/<node_id:\w+>' => '<controller>/<action>',
            ],
        ]], false);

        //API Rules
        $app->urlManager->addRules([
                'GET,HEAD,OPTIONS v1/node/<node_id:\w+>/<controller:\w+>/<action:\w+>' => 'node/api/v1/<controller>/<action>',
                'GET,HEAD,OPTIONS v1/node/<node_id:\w+>' => 'node/api/v1/ln-node/view',
                'GET,HEAD,OPTIONS v1/nodes' => 'node/api/v1/ln-node/view-all'
        ], false);
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'controllers' => ['node/*'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'controllers' => ['node/api/*'],
                        'roles' => ['?','@'],
                    ],
                ],
            ],
        ];
    }
}