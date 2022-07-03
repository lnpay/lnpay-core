<?php

return [
    [
        'name'=>'Public API Key',
        'type' =>'1',
        'description' => 'Identifies account, basic permission',
        'rule_name'=>null,
        'data'=>null,
        'created_at'=>time(),
        'updated_at'=>time()
    ],
    [
        'name'=>'Secret API Key',
        'type' =>'1',
        'description' => 'Can perform all actions',
        'rule_name'=>null,
        'data'=>null,
        'created_at'=>time(),
        'updated_at'=>time()
    ],
    [
        'name'=>'Wallet Admin',
        'type' =>'1',
        'description' => 'Can read,deposit,transfer,withdraw',
        'rule_name'=>null,
        'data'=>null,
        'created_at'=>time(),
        'updated_at'=>time()
    ],
    [
        'name'=>'Wallet Invoice',
        'type' =>'1',
        'description' => 'Can read, deposit',
        'rule_name'=>null,
        'data'=>null,
        'created_at'=>time(),
        'updated_at'=>time()
    ],
    [
        'name'=>'Wallet LNURL Withdraw',
        'type' =>'1',
        'description' => 'withdraw,txread',
        'rule_name'=>null,
        'data'=>null,
        'created_at'=>time(),
        'updated_at'=>time()
    ],
    [
        'name'=>'Wallet LNURL Pay',
        'type' =>'1',
        'description' => 'deposit',
        'rule_name'=>null,
        'data'=>null,
        'created_at'=>time(),
        'updated_at'=>time()
    ],
    [
        'name'=>'Wallet Read',
        'type' =>'1',
        'description' => 'Can read',
        'rule_name'=>null,
        'data'=>null,
        'created_at'=>time(),
        'updated_at'=>time()
    ],
    [
        'name'=>'wallet_deposit',
        'type' =>'2',
        'description' => 'Wallet Deposit',
        'rule_name'=>null,
        'data'=>null,
        'created_at'=>time(),
        'updated_at'=>time()
    ],
    [
        'name'=>'wallet_public_withdraw',
        'type' =>'2',
        'description' => 'Wallet Public Withdraw',
        'rule_name'=>null,
        'data'=>null,
        'created_at'=>time(),
        'updated_at'=>time()
    ],
    [
        'name'=>'wallet_read',
        'type' =>'2',
        'description' => 'Wallet Read',
        'rule_name'=>null,
        'data'=>null,
        'created_at'=>time(),
        'updated_at'=>time()
    ],
    [
        'name'=>'wallet_transfer',
        'type' =>'2',
        'description' => 'Wallet Transfer',
        'rule_name'=>null,
        'data'=>null,
        'created_at'=>time(),
        'updated_at'=>time()
    ],
    [
        'name'=>'wallet_tx_read',
        'type' =>'2',
        'description' => 'Wallet Tx Read',
        'rule_name'=>null,
        'data'=>null,
        'created_at'=>time(),
        'updated_at'=>time()
    ],
    [
        'name'=>'wallet_withdraw',
        'type' =>'2',
        'description' => 'Wallet Withdraw',
        'rule_name'=>null,
        'data'=>null,
        'created_at'=>time(),
        'updated_at'=>time()
    ],
];
