<?php

use yii\db\Migration;

/**
 * Class m200719_162034_lntx_fees
 */
class m200719_162034_lntx_fees extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('ln_tx','fee_msat','INT(11) DEFAULT 0 AFTER num_satoshis');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('ln_tx','fee_msat');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200719_162034_lntx_fees cannot be reverted.\n";

        return false;
    }
    */
}
