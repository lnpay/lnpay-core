<?php

namespace lnpay\wallet;

use yii\base\BootstrapInterface;
use yii\filters\AccessControl;
use yii\web\Application as WebApplication;
use yii\web\GroupUrlRule;

class Module extends \yii\base\Module implements BootstrapInterface
{
    public $controllerNamespace = 'lnpay\wallet\controllers';
    public $sidebarView = null;
    public $homeUrl = '/wallet/wallet/dashboard';

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
                '<controller:\w+>/<action\w+>' => '<controller>/<action>'
            ],
        ]], false);

        //API Rules
        $app->urlManager->addRules([
            //WALLETS NEW NEW
            'GET,HEAD,OPTIONS v1/wallets' => 'wallet/api/v1/wallet/view-all',
            'GET,HEAD,OPTIONS v1/wallet/<access_key:\w+>' => 'wallet/api/v1/wallet/view',
            'GET,OPTIONS v1/wallet/<access_key:\w+>/lnurl/withdraw-static' => 'wallet/api/v1/wallet/lnurl-withdraw-static',
            'GET,OPTIONS v1/wallet/<access_key:\w+>/lnurl/withdraw' => 'wallet/api/v1/wallet/lnurl-withdraw',
            'GET,OPTIONS v1/wallet/<access_key:\w+>/lnurl-process' => 'wallet/api/v1/wallet/lnurl-process',
            'POST,OPTIONS v1/wallet' => 'wallet/api/v1/wallet/create',
            'POST,OPTIONS v1/wallet/<access_key:\w+>/withdraw' => 'wallet/api/v1/wallet/withdraw',
            'POST,OPTIONS v1/wallet/<access_key:\w+>/keysend' => 'wallet/api/v1/wallet/keysend',
            'POST,OPTIONS v1/wallet/<access_key:\w+>/invoice' => 'wallet/api/v1/wallet/invoice',
            'POST,OPTIONS v1/wallet/<access_key:\w+>/transfer' => 'wallet/api/v1/wallet/transfer',
            'GET,OPTIONS v1/wallet/<access_key:\w+>/transactions' => 'wallet/api/v1/wallet/transactions',


            //WALLET-TRANSACTIONS
            'GET,OPTIONS v1/wallet-transactions' => 'wallet/api/v1/wallet-transaction/view-all',

            //WALLET LNURL
            'GET,OPTIONS v1/wallet/<access_key:\w+>/lnurlp/<wallet_lnurlpay_id:\w+>' => 'wallet/api/v1/lnurlpay/lnurl-process',
            'GET,OPTIONS v1/lnurlp/probe/<lnurlpayEncodedOrLnAddress:[a-zA-Z0-9_@.-]+>' => 'wallet/api/v1/lnurlpay/probe',
            'GET,OPTIONS .well-known/lnurlp/<username:\w+>' => 'wallet/api/v1/lnurlpay/lightning-address',
            'GET,OPTIONS .well-known/<custy_domain_id:\w+>/lnurlp/<username:\w+>' => 'wallet/api/v1/lnurlpay/lightning-address',
            'POST,OPTIONS v1/wallet/<access_key:\w+>/lnurlp/pay' => 'wallet/api/v1/lnurlpay/pay',
            'POST,OPTIONS v1/wallet/<access_key:\w+>/lnurlp' => 'wallet/api/v1/lnurlpay/create',
            'GET,OPTIONS v1/lnurlp/<wallet_lnurlpay_id:\w+>' => 'wallet/api/v1/lnurlpay/view',
            'POST,PUT,PATCH,OPTIONS v1/lnurlp/<wallet_lnurlpay_id:\w+>' => 'wallet/api/v1/lnurlpay/update',

            //WALLET PUBLIC
            'GET,HEAD,OPTIONS public/wallet/<access_key:\w+>' => 'wallet/pub/index/view',



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
                        'controllers' => ['wallet/*'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'controllers' => ['wallet/api/*'],
                        'roles' => ['?','@'],
                    ],
                    [
                        'allow' => true,
                        'controllers' => ['wallet/pub/*'],
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }
}