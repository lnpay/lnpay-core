<?php

use yii\db\Migration;

/**
 * Class m220203_170922_utf8mb4_encoding
 */
class m220203_170922_utf8mb4_encoding extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //issue with this table: [1071 Specified key was too long; max key length is 3072 bytes]
        //$this->execute("ALTER TABLE ln_tx CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin");

        $this->execute("ALTER TABLE action_data CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin");
        $this->execute("ALTER TABLE user_api_log CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin");
        $this->execute("ALTER TABLE wallet_transaction CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("ALTER TABLE action_data CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci");
        $this->execute("ALTER TABLE user_api_log CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci");
        $this->execute("ALTER TABLE wallet_transaction CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci");

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220203_170922_utf8mb4_encoding cannot be reverted.\n";

        return false;
    }
    */
}
