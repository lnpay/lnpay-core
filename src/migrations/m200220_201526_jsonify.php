<?php

use yii\db\Migration;

/**
 * Class m200220_201526_jsonify
 */
class m200220_201526_jsonify extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("alter table `action_data` modify `data` json");
        $this->execute("alter table `base_link` modify `json_data` json");
        $this->execute("alter table `base_link_analytics` modify `json_data` json");
        $this->execute("alter table `custy_domain` modify `data` json");
        $this->execute("alter table `integration_service` modify `json_data` json");
        $this->execute("alter table `integration_webhook` modify `json_data` json");
        $this->execute("alter table `ln_tx` modify `json_data` json");
        $this->execute("alter table `user` modify `json_data` json");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("alter table `action_data` modify `data` longtext");
        $this->execute("alter table `base_link` modify `json_data` longtext");
        $this->execute("alter table `base_link_analytics` modify `json_data` longtext");
        $this->execute("alter table `custy_domain` modify `data` longtext");
        $this->execute("alter table `integration_service` modify `json_data` longtext");
        $this->execute("alter table `integration_webhook` modify `json_data` longtext");
        $this->execute("alter table `ln_tx` modify `json_data` longtext");
        $this->execute("alter table `user` modify `json_data` longtext");

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200220_201526_jsonify cannot be reverted.\n";

        return false;
    }
    */
}
