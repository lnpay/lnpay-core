<?php

use yii\db\Migration;

/**
 * Class m211228_154357_amp_lntx_db
 */
class m211228_154357_amp_lntx_db extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('ln_tx','is_amp','bool AFTER is_keysend');
        $this->addColumn('ln_tx','payment_addr','varchar(255) AFTER description_hash');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('ln_tx','is_amp');
        $this->dropColumn('ln_tx','payment_addr');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211228_154357_amp_lntx_db cannot be reverted.\n";

        return false;
    }
    */
}
