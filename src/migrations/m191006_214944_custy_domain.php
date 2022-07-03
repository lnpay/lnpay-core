<?php

use yii\db\Migration;

/**
 * Class m191006_214944_custy_domain
 */
class m191006_214944_custy_domain extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE `status_type` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `type` varchar(255) DEFAULT NULL,
              `name` varchar(255) DEFAULT NULL,
              `display_name` varchar(255) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $this->execute("CREATE TABLE `custy_domain` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `user_id` int(11) DEFAULT NULL,
              `domain_name` varchar(255) NOT NULL DEFAULT '',
              `port` int(11) DEFAULT NULL,
              `display_name` varchar(255) NOT NULL DEFAULT '',
              `use_https` tinyint(1) DEFAULT '0',
              `ssl_info` text,
              `use_hsts` tinyint(1) DEFAULT '0',
              `upgrade_insecure` tinyint(1) DEFAULT '0',
              `status_type_id` int(11) NOT NULL,
              `data` text,
              PRIMARY KEY (`id`),
              KEY `user_id` (`user_id`),
              CONSTRAINT `custy_domain_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `custy_domain_ibfk_2` FOREIGN KEY (`status_type_id`) REFERENCES `status_type` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $this->execute("ALTER TABLE `custy_domain` AUTO_INCREMENT = 1100");

        $this->execute("INSERT INTO `status_type` (`id`, `type`, `name`, `display_name`)
                VALUES
                    (50, 'custy_domain', 'active', 'Active'),
                    (51, 'custy_domain', 'inactive', 'Inactive'),
                    (52, 'custy_domain', 'pending', 'Pending');
                ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("DROP TABLE `custy_domain`");
        $this->execute("DROP TABLE `status_type`");

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191006_214944_custy_domain cannot be reverted.\n";

        return false;
    }
    */
}
