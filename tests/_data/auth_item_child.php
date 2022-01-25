<?php

return [
    [
        'parent'=>'Wallet Admin',
        'child' =>'wallet_deposit',
    ],
    [
        'parent'=>'Wallet Invoice',
        'child' =>'wallet_deposit',
    ],
    [
        'parent'=>'Wallet LNURL Withdraw',
        'child' =>'wallet_public_withdraw',
    ],
    [
        'parent'=>'Wallet Admin',
        'child' =>'wallet_read',
    ],
    [
        'parent'=>'Wallet LNURL Withdraw',
        'child' =>'wallet_read',
    ],
    [
        'parent'=>'Wallet Read',
        'child' =>'wallet_read',
    ],
    [
        'parent'=>'Wallet Admin',
        'child' =>'wallet_transfer',
    ],
    [
        'parent'=>'Wallet Admin',
        'child' =>'wallet_tx_read',
    ],
    [
        'parent'=>'Wallet Invoice',
        'child' =>'wallet_tx_read',
    ],
    [
        'parent'=>'Wallet Read',
        'child' =>'wallet_tx_read',
    ],
    [
        'parent'=>'Wallet Admin',
        'child' =>'wallet_withdraw',
    ],
    [
        'parent'=>'Wallet LNURL Withdraw',
        'child' =>'wallet_withdraw',
    ],
    [
        'parent'=>'Wallet LNURL Pay',
        'child' =>'wallet_deposit',
    ],
];
