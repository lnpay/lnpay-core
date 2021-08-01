<?php

use yii\db\Migration;

/**
 * Class m200328_131216_yii2queue
 */
class m200328_131216_yii2queue extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE `queue` (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `channel` varchar(255) NOT NULL,
                          `job` blob NOT NULL,
                          `pushed_at` int(11) NOT NULL,
                          `ttr` int(11) NOT NULL,
                          `delay` int(11) NOT NULL DEFAULT 0,
                          `priority` int(11) unsigned NOT NULL DEFAULT 1024,
                          `reserved_at` int(11) DEFAULT NULL,
                          `attempt` int(11) DEFAULT NULL,
                          `done_at` int(11) DEFAULT NULL,
                          PRIMARY KEY (`id`),
                          KEY `channel` (`channel`),
                          KEY `reserved_at` (`reserved_at`),
                          KEY `priority` (`priority`)
                        ) ENGINE=InnoDB");

        $this->execute("CREATE TABLE `log_queue` (
                      `id` bigint(20) NOT NULL AUTO_INCREMENT,
                      `level` int(11) DEFAULT NULL,
                      `category` varchar(255) DEFAULT NULL,
                      `log_time` int(11) DEFAULT NULL,
                      `prefix` text,
                      `message` text,
                      PRIMARY KEY (`id`),
                      KEY `idx_log_level` (`level`),
                      KEY `idx_log_category` (`category`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COMMENT='1';");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("DROP TABLE `queue`");
        $this->execute("DROP TABLE `log_queue`");

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200328_131216_yii2queue cannot be reverted.\n";

        return false;
    }
    */
}
