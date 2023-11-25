<?php
use hoaaah\sbadmin2\widgets\Menu;

if ($nodes = \LNPay::$app->user->identity->lnNodes) {
    $lightningNodeMenu = [];
    foreach ($nodes as $node)
         $lightningNodeMenu[] = [
            'label' => $node->alias,
            //'url' => ['/node/dashboard/index'], //  Array format of Url to, will be not used if have an items
            'icon' => 'fas fa-fw fa-bolt', // optional, default to "fa fa-circle-o
            'visible' => true,
            'items' => [
                [
                    'label' => 'Node Dashboard',
                    'url' => ['/node/ln/index/','id'=>$node->id], //  Array format of Url to, will be not used if have an items
                ],
                [
                    'label' => 'On Chain',
                    'url' => ['/node/ln/onchain/','id'=>$node->id], //  Array format of Url to, will be not used if have an items
                ],
                [
                    'label' => 'Event Subscriptions',
                    'url' => ['/node/rpc/listeners/','id'=>$node->id], //  Array format of Url to, will be not used if have an items
                ],
                [
                    'label' => 'Settings',
                    'icon' => 'fas fa-cog', // optional, default to "fa fa-circle-o
                    'url' => ['/node/ln/settings/'.$node->id], //  Array format of Url to, will be not used if have an items
                ],
                /*[
                    'label' => 'Invoices (LnTx)',
                    'url' => ['/node/ln/index/','id'=>$node->id], //  Array format of Url to, will be not used if have an items
                ],*/
            ]
        ];
} else {
    $lightningNodeMenu = [
        [
            'label' => 'Lightning Node',
            'url' => ['/node/dashboard/index'], //  Array format of Url to, will be not used if have an items
            'icon' => 'fas fa-fw fa-bolt', // optional, default to "fa fa-circle-o
            'visible' => true,
            'items' => [
                [
                    'label' => 'Connect Node',
                    'url' => ['/node/dashboard/add'], //  Array format of Url to, will be not used if have an items
                ]
                /*[
                    'label' => 'Invoices (LnTx)',
                    'url' => ['/menu21'], //  Array format of Url to, will be not used if have an items
                ],*/
            ]
        ]
    ];
}


$items = [
        [
            'label' => 'Home',
            'url' => ['/dashboard/home'], //  Array format of Url to, will be not used if have an items
            'icon' => 'fas fa-home', // optional, default to "fa fa-circle-o
            'visible' => true, // optional, default to true
        ],
        [
            'type' => 'divider', // divider or sidebar, if not set then link menu
        ],
        [
            'label' => 'Wallets',
            'url' => ['/wallet/wallet/dashboard'], //  Array format of Url to, will be not used if have an items
            'icon' => 'fas fa-wallet', // optional, default to "fa fa-circle-o
            'visible' => true, // optional, default to true
            // 'options' => [
            //     'liClass' => 'nav-item',
            // ] // optional
        ],
        [
            'label' => 'Transactions',
            'url' => ['/wallet/wallet-transaction'], //  Array format of Url to, will be not used if have an items
            'icon' => 'fas fa-fw fa-exchange-alt', // optional, default to "fa fa-circle-o
            'visible' => true, // optional, default to true
            // 'options' => [
            //     'liClass' => 'nav-item',
            // ] // optional
        ],
        [
            'type' => 'divider', // divider or sidebar, if not set then link menu
            // 'label' => '', // if sidebar we will set this, if divider then no
        ],
    ];
    /*[
        'label' => 'Lightning Address',
        'url' => ['/menu1'], //  Array format of Url to, will be not used if have an items
        'icon' => 'fas fa-fw fa-envelope', // optional, default to "fa fa-circle-o
        'visible' => true, // optional, default to true
        // 'options' => [
        //     'liClass' => 'nav-item',
        // ] // optional
    ],
    [
        'label' => 'LNURL',
        'visible' => true, // optional, default to true
        // 'subMenuTitle' => 'Menu 3 Item', // optional only when have submenutitle, if not exist will not have subMenuTitle
        'items' => [
            [
                'label' => 'LNURL Pay',
                'url' => ['/menu21'], //  Array format of Url to, will be not used if have an items
            ],
            [
                'label' => 'LNURL Withdraw',
                'url' => ['/menu22'], //  Array format of Url to, will be not used if have an items
                'icon' => 'fas fa-fw fa-tachometer-alt',
            ],
        ]
    ],
    [
        'label' => 'Keysend',
        'url' => ['/menu1'], //  Array format of Url to, will be not used if have an items
        'icon' => 'fas fa-fw fa-arrow-right', // optional, default to "fa fa-circle-o
        'visible' => true, // optional, default to true
        // 'options' => [
        //     'liClass' => 'nav-item',
        // ] // optional
    ],*/

    $items = \yii\helpers\ArrayHelper::merge(
        $items,
        $lightningNodeMenu,
        [
            [
                'type' => 'divider', // divider or sidebar, if not set then link menu
                // 'label' => '', // if sidebar we will set this, if divider then no

            ],
            [
                'label' => 'Webhooks',
                'url' => ['/webhook/index'], //  Array format of Url to, will be not used if have an items
                'icon' => 'fas fa-fw fa-tachometer-alt', // optional, default to "fa fa-circle-o
                'visible' => true, // optional, default to true
                // 'options' => [
                //     'liClass' => 'nav-item',
                // ] // optional
            ],
            [
                'label' => 'Domains',
                'url' => ['/domain/index'], //  Array format of Url to, will be not used if have an items
                'icon' => 'fas fa-fw fa-server', // optional, default to "fa fa-circle-o
                'visible' => true, // optional, default to true
                // 'options' => [
                //     'liClass' => 'nav-item',
                // ] // optional
            ],
            [
                'label' => 'API Logs',
                'url' => ['/api-log/index'], //  Array format of Url to, will be not used if have an items
                'icon' => 'fas fa-fw fa-history', // optional, default to "fa fa-circle-o
                'visible' => true, // optional, default to true
                // 'options' => [
                //     'liClass' => 'nav-item',
                // ] // optional
            ],
            [
                'label' => 'API Keys',
                'url' => ['/developers/dashboard'], //  Array format of Url to, will be not used if have an items
                'icon' => 'fas fa-fw fa-key', // optional, default to "fa fa-circle-o
                'visible' => true, // optional, default to true
                // 'options' => [
                //     'liClass' => 'nav-item',
                // ] // optional
            ]
        ]
    );






echo Menu::widget([
    'options' => [
        'ulClass' => "navbar-nav bg-gradient-primary sidebar sidebar-dark accordion",
        'ulId' => "accordionSidebar"
    ], //  optional
    'brand' => [
        'url' => ['/'],
        'content' => <<<HTML
            <div class="sidebar-brand-icon">
            <img src="/img/LNPay-Logo-1200_256x256.png" width="30"/>
            <div class="sidebar-brand-text">LNPay</div>     
            </div>
   
HTML
    ],
    'items' => $items
]);

$this->registerJs("$('.sidebar a[href^=\"' + location.pathname + '\"').addClass('active');");
$this->registerJs("$('.sidebar a[href^=\"' + location.pathname + '\"').parent().parent().addClass('show');");