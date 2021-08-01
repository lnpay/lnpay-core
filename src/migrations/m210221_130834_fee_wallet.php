<?php

use yii\db\Migration;

/**
 * Class m210221_130834_fee_wallet
 */
class m210221_130834_fee_wallet extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("INSERT INTO `wallet_transaction_type` (`id`, `layer`, `name`, `display_name`)
                        VALUES
                            (70, 'ln', 'network_fee', 'LN Routing Fees'),
                               (75, 'ln', 'service_fee', 'LNPAY Service Fee'),
                               (79, 'ln', 'fee_balance_payment', 'Fee Balance Payment');
                        ");

        $this->execute("ALTER TABLE `wallet` ADD COLUMN `balance_msat` INT(11) AFTER `balance`");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("ALTER TABLE `wallet` DROP COLUMN `balance_msat`");
        $this->execute("DELETE FROM `wallet_transaction_type` WHERE id IN(70,75,79)");

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210221_130834_fee_wallet cannot be reverted.\n";

        return false;
    }
    */
}
