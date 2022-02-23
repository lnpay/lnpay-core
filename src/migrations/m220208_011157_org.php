<?php

use yii\db\Migration;

/**
 * Class m220208_011157_org
 */
class m220208_011157_org extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE `org_user_type` (
                  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                  `name` varchar(255) DEFAULT NULL,
                  `display_name` varchar(255) DEFAULT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4;");

        $this->execute("INSERT INTO `org_user_type` (`id`, `name`, `display_name`)
                        VALUES
                            (20, 'owner', 'Owner'),
                            (30, 'admin', 'Administrator'),
                            (40, 'developer', 'Developer'),
                            (50, 'app_user', 'App-Scoped User');
                        ");

        $this->execute("CREATE TABLE `org` (
                  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                  `created_at` int(11) DEFAULT NULL,
                  `updated_at` int(11) DEFAULT NULL,
                  `name` varchar(255) NOT NULL DEFAULT '',
                  `display_name` varchar(255) NOT NULL DEFAULT '',
                  `external_hash` varchar(128) NOT NULL DEFAULT '',
                  `status_type_id` int(11) NOT NULL,
                  `json_data` json DEFAULT NULL,
                  PRIMARY KEY (`id`),
                  KEY `status_type_id` (`status_type_id`),
                  CONSTRAINT `org_ibfk_1` FOREIGN KEY (`status_type_id`) REFERENCES `status_type` (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $this->addColumn('user','org_id','int(11) unsigned AFTER api_version');
        $this->addColumn('user','org_user_type_id','int(11) unsigned AFTER org_id');

        $this->addForeignKey('user_ibfk_5','user','org_id','org','id','RESTRICT','RESTRICT');
        $this->addForeignKey('user_ibfk_6','user','org_user_type_id','org_user_type','id','RESTRICT','RESTRICT');

        $this->addColumn('ln_node','org_id','int(11) unsigned AFTER user_id');
        $this->addForeignKey('ln_node_ibfk_10','ln_node','org_id','org','id','RESTRICT','RESTRICT');

        $this->insert('status_type',[
            'id'=>500,
            'type'=>'org',
            'name'=>'org_active',
            'display_name'=>'Org Active',
        ]);

        $this->insert('status_type',[
            'id'=>510,
            'type'=>'org',
            'name'=>'org_inactive',
            'display_name'=>'Org Inactive',
        ]);

        $this->addColumn('ln_node','is_custodian','bool AFTER org_id');

        //create default orgs for users
            //populate user with default org
        $this->insert('org',[
            'id'=>2000,
            'created_at'=>time(),
            'updated_at'=>time(),
            'name'=>'lnpay_default_org',
            'display_name'=>'LNPay Organization',
            'external_hash'=>'org_EoqWo3Gs',
            'status_type_id'=>500
        ]);

        \lnpay\models\User::updateAll(['org_id'=>2000,'org_user_type_id'=>40]);

        //on push for prod migrate
        //Set lnpay user as default org owner
        //set lnpay nodes is_custodian appropriately
        //1. Create new orgs per users with LN node
        //2. Update user table to reflect org association
        //3. Update node table to reflect org association

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('org',['id'=>2000]);
        $this->dropForeignKey('ln_node_ibfk_10','ln_node');
        $this->dropColumn('ln_node','org_id');
        $this->dropColumn('ln_node','is_custodian');
        $this->dropForeignKey('user_ibfk_5','user');
        $this->dropForeignKey('user_ibfk_6','user');
        $this->dropColumn('user','org_id');
        $this->dropColumn('user','org_user_type_id');
        $this->dropTable('org_user_type');
        $this->dropTable('org');

        $this->delete('status_type',['type'=>'org']);



        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220208_011157_org cannot be reverted.\n";

        return false;
    }
    */
}
