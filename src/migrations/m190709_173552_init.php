<?php

use yii\db\Migration;

/**
 * Class m190709_173552_init
 */
class m190709_173552_init extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            -- Create syntax for TABLE 'cache'            
            CREATE TABLE cache (
                id char(128) NOT NULL PRIMARY KEY,
                expire int(11),
                data BLOB
            );");

        $this->execute("
                    -- Create syntax for TABLE 'user'
                            CREATE TABLE `user` (
                              `id` int(11) NOT NULL AUTO_INCREMENT,
                              `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                              `auth_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
                              `password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                              `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
                              `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                              `status` smallint(6) NOT NULL DEFAULT '10',
                              `created_at` int(11) NOT NULL,
                              `updated_at` int(11) NOT NULL,
                              `fee_wallet_id` int(11),
                              `balance` int(11) DEFAULT '0',
                              PRIMARY KEY (`id`),
                              UNIQUE KEY `username` (`username`),
                              UNIQUE KEY `email` (`email`),
                              UNIQUE KEY `password_reset_token` (`password_reset_token`)
                            ) ENGINE=InnoDB AUTO_INCREMENT=152 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
                            
                    -- Create syntax for TABLE 'log'
                            CREATE TABLE `log` (
                              `id` bigint(20) NOT NULL AUTO_INCREMENT,
                              `level` int(11) DEFAULT NULL,
                              `category` varchar(255) DEFAULT NULL,
                              `log_time` int(11) DEFAULT NULL,
                              `prefix` text,
                              `message` text,
                              PRIMARY KEY (`id`),
                              KEY `idx_log_level` (`level`),
                              KEY `idx_log_category` (`category`)
                            ) ENGINE=InnoDB AUTO_INCREMENT=155 DEFAULT CHARSET=latin1 COMMENT='1';
        ");

        $this->execute("CREATE TABLE session
            (
                id CHAR(40) NOT NULL PRIMARY KEY,
                expire INTEGER,
                data BLOB
            )");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190709_173552_init cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190709_173552_init cannot be reverted.\n";

        return false;
    }
    */
}
