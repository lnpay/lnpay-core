<?php

use yii\db\Migration;

/**
 * Class m220426_174013_wallet_external_admin
 */
class m220426_174013_wallet_external_admin extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //create Wallet External Admin
        $auth = \LNPay::$app->authManager;

        $authRole = $auth->createRole(\lnpay\behaviors\UserAccessKeyBehavior::ROLE_WALLET_EXTERNAL_WEBSITE_ADMIN);
        $authRole->description = 'Can perform all actions on public website';
        $auth->add($authRole);
        $auth->addChild($authRole, $auth->getPermission('wallet_deposit'));
        $auth->addChild($authRole, $auth->getPermission('wallet_withdraw'));
        $auth->addChild($authRole, $auth->getPermission('wallet_read'));
        $auth->addChild($authRole, $auth->getPermission('wallet_tx_read'));

        $authRole = $auth->createRole(\lnpay\behaviors\UserAccessKeyBehavior::ROLE_WALLET_EXTERNAL_WEBSITE_VIEW);
        $authRole->description = 'Can view wallet on public website';
        $auth->add($authRole);
        $auth->addChild($authRole, $auth->getPermission('wallet_read'));
        $auth->addChild($authRole, $auth->getPermission('wallet_tx_read'));

        foreach (\lnpay\wallet\models\Wallet::find()->each() as $w) {

            try {
                //Add new wallet external key
                \lnpay\models\UserAccessKey::createKey($w->user_id,\lnpay\behaviors\UserAccessKeyBehavior::ROLE_WALLET_EXTERNAL_WEBSITE_VIEW,['wallet_id'=>$w->id]);
                \lnpay\models\UserAccessKey::createKey($w->user_id,\lnpay\behaviors\UserAccessKeyBehavior::ROLE_WALLET_EXTERNAL_WEBSITE_ADMIN,['wallet_id'=>$w->id]);
            } catch ( \Throwable $t)
            {
                // not much to do at this point
                echo $t;
            }

        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('auth_item',['name'=>\lnpay\behaviors\UserAccessKeyBehavior::ROLE_WALLET_EXTERNAL_WEBSITE_VIEW]);
        $this->delete('auth_item',['name'=>\lnpay\behaviors\UserAccessKeyBehavior::ROLE_WALLET_EXTERNAL_WEBSITE_ADMIN]);

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220426_174013_wallet_external_admin cannot be reverted.\n";

        return false;
    }
    */
}
