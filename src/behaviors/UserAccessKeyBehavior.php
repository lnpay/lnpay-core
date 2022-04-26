<?php

namespace lnpay\behaviors;

use lnpay\models\UserAccessKey;
use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;
use yii\base\Behavior;

use Yii;

/**
 *
 *
 * @author Tim Kijewski <bootstrapbandit7@gmail.com>
 */
class UserAccessKeyBehavior extends Behavior
{
    const ROLE_PUBLIC_API_KEY = 'Public API Key';
    const ROLE_SECRET_API_KEY = 'Secret API Key';

    const ROLE_WALLET_ADMIN = 'Wallet Admin';
    const ROLE_WALLET_INVOICE = 'Wallet Invoice';
    const ROLE_WALLET_READ = 'Wallet Read';
    const ROLE_WALLET_LNURL_WITHDRAW = 'Wallet LNURL Withdraw';
    const ROLE_WALLET_LNURL_PAY = 'Wallet LNURL Pay';
    const ROLE_WALLET_EXTERNAL_WEBSITE_ADMIN = 'Wallet External Website Admin';
    const ROLE_WALLET_EXTERNAL_WEBSITE_VIEW = 'Wallet External Website View';

    const ROLE_KEY_SUSPENDED = 'Key Suspended';

    const PERM_WALLET_READ = 'wallet_read'; //read / wallet info
    const PERM_WALLET_TX_READ = 'wallet_tx_read'; //read single transaction
    const PERM_WALLET_DEPOSIT = 'wallet_deposit';
    const PERM_WALLET_TRANSFER = 'wallet_transfer';
    const PERM_WALLET_WITHDRAW = 'wallet_withdraw';
    const PERM_WALLET_PUBLIC_WITHDRAW = 'wallet_public_withdraw';

    const PERM_DEFAULT_NODE_WRAPPER_ACCESS = 'default_node_wrapper_access';

    /**
     * @var null The column name of the object that this key is tied to e.g. wallet_id
     * Set null if this is key is for just the user with no other object
     */
    public $_accessKeyColumnName = NULL;

    /**
     * @var array These roles are automatically created when the parent object is first inserted
     */
    public $default_roles = [];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
    }

    public function events()
    {
        $events=[
            BaseActiveRecord::EVENT_AFTER_INSERT=>'afterSaveInsert'
        ];
        return $events;
    }

    protected function setAccessKeyColumnName($name)
    {
        $this->_accessKeyColumnName = $name;
    }

    public function getAccessKeyColumnName()
    {
        if ($this->_accessKeyColumnName)
            return $this->_accessKeyColumnName;

        if ($this->owner::tableName() == '{{%user}}')
            return NULL;

        return $this->owner::tableName().'_id';
    }

    public function getAvailableAccessKeyColumnNames()
    {
        return ['wallet_id'];
    }

    public function getUserId()
    {
        if ($this->owner::tableName() == '{{%user}}')
            return $this->owner->id;
        else
            return $this->owner->user_id;
    }

    /**
     * @return array of keys
     * If byRole is specified, same structure is returned but with only appropriate roles/keys
     * Array
        (
            [Wallet Admin] => Array
            (
                [0] => wa_gy6aKnNfvPhFm1p8VF3YnjG
            )

            [Wallet Read] => Array
            (
                [0] => wr_371d5XMr39qYKtjiM8auXrr
            )

            [Wallet Invoice] => Array
            (
                [0] => wi_eQoMQsX6OpWTPy6LjlYDbuxj
            )
        )
     */
    public function getUserAccessKeys($byRole=null)
    {
        $auth = \LNPay::$app->authManager;

        if ($this->accessKeyColumnName)
            $uaks = UserAccessKey::find()->where(['user_id'=>$this->userId,$this->accessKeyColumnName=>$this->owner->id]);
        else {
            //If it is a user key, we need to look where all other columns are null
            $uaks = UserAccessKey::find()->where(['user_id'=>$this->userId]);
            foreach ($this->availableAccessKeyColumnNames as $column) {
                $uaks->andWhere([$column=>NULL]);
            }

        }


        $array = [];
        foreach ($uaks->all() as $uak) {
            foreach ($auth->getRolesByUser($uak->access_key) as $role) {
                $array[$role->name][] = $uak->access_key;
            }
        }

        if ($byRole) {
            if (@$array[$byRole]) {
                $arr[$byRole] = $array[$byRole];
                return $arr;
            }
            else
                return [$byRole=>[]];
        }

        return $array;
    }

    public function getFirstAccessKeyByRole($role)
    {
        return @$this->getUserAccessKeys($role)[$role][0];
    }

    /**
     * @throws \yii\web\ServerErrorHttpException
     *
     * Creates access keys in UserAccessKey table
     * Also creates roles in auth_assignment table
     */
    public function populateApiKeys()
    {
        foreach ($this->default_roles as $role) {
            if ($this->accessKeyColumnName)
                $attrs = [$this->accessKeyColumnName=>$this->owner->id];
            else
                $attrs = [];

            $wuk = UserAccessKey::createKey($this->userId,$role,$attrs);
        }
    }

    public static function checkKeyAccess($item,$access_key)
    {
        return \LNPay::$app->authManager->checkAccess($access_key,$item);
    }

    public static function getAccessKeyPrefix($access_key)
    {
        return @explode('_',$access_key)[0].'_';
    }

    public function afterSaveInsert($event)
    {
        try {
            $this->populateApiKeys();
        } catch (\Throwable $t) {
            \LNPay::error($t,__METHOD__);
        }
    }
}
