<?php

use lnpay\models\UserAccessKey;
use yii\db\Migration;

/**
 * Class m210423_153759_lnurl_withdraw_role
 */
class m210423_153759_lnurl_withdraw_role extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = \LNPay::$app->authManager;

        // add "createPost" permission
        $public_withdraw = $auth->createPermission('wallet_public_withdraw');
        $public_withdraw->description = 'Wallet Public Withdraw';
        $auth->add($public_withdraw);

        $lnurl_withdraw = $auth->createRole('Wallet LNURL Withdraw');
        $lnurl_withdraw->description = 'read,withdraw';
        $auth->add($lnurl_withdraw);
        $auth->addChild($lnurl_withdraw, $auth->getPermission('wallet_withdraw'));
        $auth->addChild($lnurl_withdraw, $auth->getPermission('wallet_read'));
        $auth->addChild($lnurl_withdraw, $public_withdraw);

        foreach (\lnpay\wallet\models\Wallet::find()->each() as $w) {
            UserAccessKey::createKey($w->user_id,'Wallet LNURL Withdraw',['wallet_id'=>$w->id]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('auth_item',['name'=>'Wallet LNURL Withdraw']);
        $this->delete('auth_item',['name'=>'wallet_public_withdraw']);

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210423_153759_lnurl_withdraw_role cannot be reverted.\n";

        return false;
    }
    */
}
