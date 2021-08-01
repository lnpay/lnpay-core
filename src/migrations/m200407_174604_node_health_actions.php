<?php

use yii\db\Migration;

/**
 * Class m200407_174604_node_health_actions
 */
class m200407_174604_node_health_actions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('action_name',[
            'id'=>'ln_node_connection_error',
            'type'=>'ln_node',
            'name'=>'ln_node_connection_error',
            'display_name'=>'LN Node Connection Error',
            'is_webhook'=>1
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('action_name',['id'=>'ln_node_connection_error']);

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200407_174604_node_health_actions cannot be reverted.\n";

        return false;
    }
    */
}
