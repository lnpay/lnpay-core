<?php

use yii\db\Migration;

/**
 * Class m190813_234443_api_parent
 */
class m190813_234443_api_parent extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE `user` ADD COLUMN `api_parent_id`  INT(11) AFTER `id`');
        $this->execute('ALTER TABLE `user` ADD COLUMN `json_data` TEXT');
        $this->execute("ALTER TABLE `user` ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`api_parent_id`) REFERENCES `user` (`id`)");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("ALTER TABLE `user` DROP FOREIGN KEY `user_ibfk_1`");
        $this->execute("ALTER TABLE `user` DROP `api_parent_id`");
        $this->execute("ALTER TABLE `user` DROP `json_data`");

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190813_234443_api_parent cannot be reverted.\n";

        return false;
    }
    */
}
