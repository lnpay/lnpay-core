<?php

use yii\db\Migration;

/**
 * Class m200120_151707_pw_link_type
 */
class m200120_151707_pw_link_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("INSERT INTO `action_name` (`id`, `type`, `name`, `display_name`)
                        VALUES
                            (405, 'user', 'pw_reset', 'Password Reset');
                        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("DELETE FROM `action_name` WHERE id = 405");

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200120_151707_pw_link_type cannot be reverted.\n";

        return false;
    }
    */
}
