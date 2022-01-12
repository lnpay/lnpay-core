<?php

use yii\db\Migration;

/**
 * Class m210414_152818_wallet_type
 */
class m210414_152818_wallet_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE `wallet_type` (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `name` varchar(255) DEFAULT NULL,
                          `display_name` varchar(255) DEFAULT NULL,
                          PRIMARY KEY (`id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $this->execute("ALTER TABLE `wallet` ADD COLUMN `wallet_type_id` INT(11) AFTER `external_hash`");
        $this->execute("ALTER TABLE `wallet` ADD CONSTRAINT `wallet_ibfk_4` FOREIGN KEY (`wallet_type_id`) REFERENCES `wallet_type` (`id`)");

        $this->batchInsert('wallet_type',['id','name','display_name'],[
            [
                'id'=>5,
                'name'=>'generic_wallet',
                'display_name'=>'Generic Wallet'
            ],
            [
                'id'=>15,
                'name'=>'fee_wallet',
                'display_name'=>'Fee Wallet'
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("ALTER TABLE `wallet` DROP FOREIGN KEY `wallet_ibfk_4`");
        $this->execute("ALTER TABLE `wallet` DROP COLUMN `wallet_type_id`");

        $this->dropTable('wallet_type');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210414_152818_wallet_type cannot be reverted.\n";

        return false;
    }
    */
}
