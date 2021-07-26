<?php

use yii\db\Migration;

/**
 * Class m191120_174352_faucet_actions
 */
class m191120_174352_action_types extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE `action_name` ADD COLUMN `type` INT DEFAULT NULL AFTER `id`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("ALTER TABLE `action_name` DROP `type`");

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191120_174352_faucet_actions cannot be reverted.\n";

        return false;
    }
    */
}
