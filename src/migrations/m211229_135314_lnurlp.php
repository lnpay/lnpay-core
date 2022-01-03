<?php

use yii\db\Migration;

/**
 * Class m211229_135314_lnurlp
 */
class m211229_135314_lnurlp extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //create wallet_lnurlpay table
        $this->execute("CREATE TABLE `wallet_lnurlpay` (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `created_at` int(11) NOT NULL,
                            `updated_at` int(11) NOT NULL,
                            `user_id` int(11) NOT NULL,
                            `wallet_id` int(11) NOT NULL,
                            `user_label` varchar(255) DEFAULT NULL,
                            `status_type_id` int(11) NOT NULL,
                            `external_hash` varchar(45) NOT NULL,
                            `json_data` json DEFAULT NULL,
                            `lnurl_encoded` text,
                            `lnurl_decoded` text,
                            `lnurlp_minSendable_msat` int(11) DEFAULT NULL,
                            `lnurlp_maxSendable_msat` int(11) DEFAULT NULL,
                            `lnurlp_short_desc` text,
                            `lnurlp_successAction` json DEFAULT NULL,
                            `lnurlp_identifier` varchar(255) DEFAULT NULL,
                            `lnurlp_commentAllowed` int(11) DEFAULT NULL,
                            `lnurlp_success_message` text,
                            `lnurlp_success_url` text,
                            `lnurlp_image_base64` text,
                            `lnurlp_metadata` text DEFAULT NULL,
                            PRIMARY KEY (`id`),
                            KEY `user_id` (`user_id`),
                            KEY `wallet_id` (`wallet_id`),
                            KEY `status_type_id` (`status_type_id`),
                            CONSTRAINT `wallet_lnurlpay_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                            CONSTRAINT `wallet_lnurlpay_ibfk_2` FOREIGN KEY (`wallet_id`) REFERENCES `wallet` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                            CONSTRAINT `wallet_lnurlpay_ibfk_3` FOREIGN KEY (`status_type_id`) REFERENCES `status_type` (`id`)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        //create wallet_lnurlw table, but not all the way
        $this->execute("CREATE TABLE `wallet_lnurlw` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `created_at` int(11) NOT NULL,
                      `updated_at` int(11) NOT NULL,
                      `user_id` int(11) NOT NULL,
                      `wallet_id` int(11) NOT NULL,
                      `status_type_id` int(11) NOT NULL,
                      `external_hash` varchar(45) NOT NULL,
                      `json_data` json DEFAULT NULL,
                      `lnurl_encoded` text,
                      `lnurl_decoded` text,
                      `lnurlw_minWithdrawable_msat` int(11) DEFAULT NULL,
                      `lnurlw_maxWithdrawable_msat` int(11) DEFAULT NULL,
                      `lnurlw_defaultDescription` text,
                      PRIMARY KEY (`id`),
                      KEY `user_id` (`user_id`),
                      KEY `wallet_id` (`wallet_id`),
                      KEY `status_type_id` (`status_type_id`),
                      CONSTRAINT `wallet_lnurlw_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                      CONSTRAINT `wallet_lnurlw_ibfk_2` FOREIGN KEY (`wallet_id`) REFERENCES `wallet` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                      CONSTRAINT `wallet_lnurlw_ibfk_3` FOREIGN KEY (`status_type_id`) REFERENCES `status_type` (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        //add wallet_lnurlpay_id, wallet_lnurlw_id column to wallet_transaction table
        $this->addColumn('wallet_transaction','wallet_lnurlpay_id','int(11) AFTER external_hash');
        $this->addColumn('wallet_transaction','wallet_lnurlw_id','int(11) AFTER wallet_lnurlpay_id');

        //add wallet_lnurl_active / wallet_lnurl_inactive to status_type table
        $this->insert('status_type',
            [
                'id'=>450,
                'type'=>'lnurl',
                'name'=>'lnurl_active',
                'display_name'=>'LNURL Active',
            ]
        );

        $this->insert('status_type',[
            'id'=>455,
            'type'=>'lnurl',
            'name'=>'lnurl_inactive',
            'display_name'=>'LNURL Inactive',
        ]);

        //create Wallet LNURL Pay auth_item
        //add wallet_deposit, wallet_read as invoice children for Wallet LNURL Pay
        $auth = \LNPay::$app->authManager;

        $lnurl_pay = $auth->createRole('Wallet LNURL Pay');
        $lnurl_pay->description = 'deposit';
        $auth->add($lnurl_pay);
        $auth->addChild($lnurl_pay, $auth->getPermission('wallet_deposit'));

        foreach (\lnpay\wallet\models\Wallet::find()->each() as $w) {
            \lnpay\models\UserAccessKey::createKey($w->user_id,'Wallet LNURL Pay',['wallet_id'=>$w->id]);
        }

        //add default_wallet to user table
        $this->addColumn('user','default_wallet_id','int(11) AFTER password_reset_token');

        //add default_lnurlpay_id, default_lnurlw_id, to wallet table
        $this->addColumn('wallet','default_lnurlpay_id','int(11) AFTER external_hash');
        $this->addColumn('wallet','default_lnurlw_id','int(11) AFTER external_hash');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        //remove default_lnurlpay_id, default_lnurlw_id, to wallet table
        $this->dropColumn('wallet','default_lnurlpay_id');
        $this->dropColumn('wallet','default_lnurlw_id');

        //remove default_wallet to user table
        $this->dropColumn('user','default_wallet_id');

        //remove Wallet LNURL Pay auth_item
        //@TODO: clean up old values in user_access_key table
        $this->delete('auth_item',['name'=>'Wallet LNURL Pay']);

        //remove wallet_lnurl_active / wallet_lnurl_inactive to status_type table
        $this->delete('status_type',['id'=>[450,455]]);

        //remove wallet_lnurlpay_id, wallet_lnurlw_id column to wallet_transaction table
        $this->dropColumn('wallet_transaction','wallet_lnurlpay_id');
        $this->dropColumn('wallet_transaction','wallet_lnurlw_id');

        //drop wallet_lnurlw table, but not all the way
        $this->dropTable('wallet_lnurlw');

        //drop wallet_lnurlpay table
        $this->dropTable('wallet_lnurlpay');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211229_135314_lnurlp cannot be reverted.\n";

        return false;
    }
    */
}
