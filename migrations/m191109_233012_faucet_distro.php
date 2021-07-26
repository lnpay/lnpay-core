<?php

use yii\db\Migration;

/**
 * Class m191109_233012_faucet_distro
 */
class m191109_233012_faucet_distro extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE `distro_method` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `name` varchar(255) NOT NULL DEFAULT '',
                      `display_name` varchar(255) NOT NULL DEFAULT '',
                      PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");


        $this->execute("INSERT INTO `status_type` (`id`, `type`, `name`, `display_name`)
                        VALUES
                            (170, 'distro_method', 'active', 'Active'),
                            (175, 'distro_method', 'inactive', 'Inactive');
                        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('distro_method');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191109_233012_faucet_distro cannot be reverted.\n";

        return false;
    }
    */
}
