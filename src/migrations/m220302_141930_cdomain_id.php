<?php

use yii\db\Migration;

/**
 * Class m220302_141930_cdomain_id
 */
class m220302_141930_cdomain_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('custy_domain','external_hash','varchar(255) AFTER status_type_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('custy_domain','external_hash');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220302_141930_cdomain_id cannot be reverted.\n";

        return false;
    }
    */
}
