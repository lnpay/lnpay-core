<?php

use yii\db\Migration;

/**
 * Class m200421_152700_wallet_node
 */
class m200421_152700_wallet_node extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE `wallet` CHANGE COLUMN `node_id` `ln_node_id` VARBINARY(64)');
        $this->addForeignKey('wallet_ibfk_3','wallet','ln_node_id','ln_node','id','RESTRICT','RESTRICT');

        $this->addColumn('ln_tx','ln_node_id','VARBINARY(64) AFTER `user_id`');
        $this->addForeignKey('ln_tx_ibfk_2','ln_tx','ln_node_id','ln_node','id','RESTRICT','RESTRICT');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('wallet_ibfk_3','wallet');
        $this->execute('ALTER TABLE `wallet` CHANGE COLUMN `ln_node_id` `node_id` INT(11)');

        $this->dropForeignKey('ln_tx_ibfk_2','ln_tx');
        $this->dropColumn('ln_tx','ln_node_id');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200421_152700_wallet_node cannot be reverted.\n";

        return false;
    }
    */
}
