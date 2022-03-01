<?php

use yii\db\Migration;

/**
 * Class m220225_185302_lnurlpay_domain
 */
class m220225_185302_lnurlpay_domain extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('wallet_lnurlpay','custy_domain_id','int(11) AFTER lnurlp_short_desc');
        $this->addForeignKey('wallet_lnurlpay_ibfk_5','wallet_lnurlpay','custy_domain_id','custy_domain','id','RESTRICT','RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('wallet_lnurlpay_ibfk_5','wallet_lnurlpay');
        $this->dropColumn('wallet_lnurlpay','custy_domain_id');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220225_185302_lnurlpay_domain cannot be reverted.\n";

        return false;
    }
    */
}
