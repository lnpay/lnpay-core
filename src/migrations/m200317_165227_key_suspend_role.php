<?php

use yii\db\Migration;

/**
 * Class m200317_165227_key_suspend_role
 */
class m200317_165227_key_suspend_role extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("INSERT INTO `auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`)
                        VALUES
                            ('Key Suspended', 1, 'Stops usage of key', NULL, NULL, 1580216401, 1580216401);
                        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("DELETE FROM `auth_item` WHERE `name` = 'Key Suspended'");

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200317_165227_key_suspend_role cannot be reverted.\n";

        return false;
    }
    */
}
