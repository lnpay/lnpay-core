<?php

use yii\db\Migration;

/**
 * Class m200714_112957_2fa
 */
class m200714_112957_2fa extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user','mfa_secret_key','VARCHAR(255) DEFAULT NULL AFTER auth_key');
        $this->addColumn('user','email_verify','BOOL DEFAULT 0 AFTER email');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user','mfa_secret_key');
        $this->dropColumn('user','email_verify');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200714_112957_2fa cannot be reverted.\n";

        return false;
    }
    */
}
