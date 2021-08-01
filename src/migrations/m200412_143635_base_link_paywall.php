<?php

use yii\db\Migration;

/**
 * Class m200412_143635_base_link_paywall
 */
class m200412_143635_base_link_paywall extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('queue','job','LONGBLOB');

        $this->execute("CREATE TABLE `link_type` (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `name` varchar(255) DEFAULT NULL,
                          `display_name` varchar(255) DEFAULT NULL,
                          `description` text,
                          `metadata` text,
                          PRIMARY KEY (`id`)
                        ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;");

        $this->addColumn('base_link','link_type_id','INT AFTER status_type_id');
        $this->addForeignKey('base_link_ibfk_3','base_link','link_type_id','link_type','id','RESTRICT','RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('queue','job','BLOB');

        $this->dropForeignKey('base_link_ibfk_3','base_link');
        $this->dropColumn('base_link','link_type_id');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200412_143635_base_link_paywall cannot be reverted.\n";

        return false;
    }
    */
}
