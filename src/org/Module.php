<?php

namespace lnpay\org;

use yii\base\BootstrapInterface;
use yii\filters\AccessControl;
use yii\web\Application as WebApplication;
use yii\web\GroupUrlRule;

class Module extends \yii\base\Module implements BootstrapInterface
{
    public $controllerNamespace = 'lnpay\org\controllers';
    public $sidebarView = '@app/org/views/_nav-org.php';
    public $homeUrl = '/org/home/view';

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
                '<controller:\w+>/<action\w+>' => '<controller>/<action>',
            ],
        ]], false);
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'controllers' => ['org/*'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
}