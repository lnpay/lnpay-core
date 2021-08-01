<?php

use yii\db\Migration;

/**
 * Class m191129_223556_action_cleanup
 */
class m191129_223556_action_cleanup extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `action_name` CHANGE `type` `type` VARCHAR(255);");
        $this->execute("INSERT INTO `action_name` (`id`, `type`, `name`, `display_name`)
                        VALUES
                            (400, 'user', 'user_created', 'User Created');
                        ");
        $this->execute("UPDATE `action_name` SET `type` = 'user' WHERE id IN (420,430,410)");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("UPDATE `action_name` SET `type` = NULL");
        $this->execute("DELETE FROM `action_name` WHERE id = 400");

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191129_223556_action_cleanup cannot be reverted.\n";

        return false;
    }
    */
}
