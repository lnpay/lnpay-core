<?php
namespace lnpay\wallet\models;


use lnpay\components\HelperComponent;
use lnpay\models\action\ActionName;
use lnpay\node\models\LnNode;
use yii\base\Model;
use lnpay\models\User;
use yii\helpers\ArrayHelper;


/**
 * WalletNodeChangeForm form
 */
class WalletNodeChangeForm extends Model
{
    /**
     * Public IDs used here
     */

    /**
     * @var null
     */
    public $target_ln_node_id = NULL;

    /**
     * @var null
     */
    public $wallet_id = NULL;

    /**
     * @var null
     */
    public $transfer_balance = NULL;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['target_ln_node_id','wallet_id'], 'required'],
            [['wallet_id','target_ln_node_id'],'string'],
            ['transfer_balance','boolean'],
            ['wallet_id','checkWallet'],
            ['target_ln_node_id','checkNode']
        ];
    }

    public function attributeLabels()
    {
        return [
            'target_ln_node_id'=>'Target Node ID',
            'wallet_id'=>'Wallet ID',
            'transfer_balance' => 'Transfer the balance to new node'
        ];
    }

    public function getWalletObject()
    {
        return Wallet::find()->where(['external_hash'=>$this->wallet_id,'user_id'=>\LNPay::$app->user->id])->one();
    }

    public function checkWallet()
    {
        if (!$this->walletObject)
            throw new \yii\web\ServerErrorHttpException('Unknown wallet, cannot continue!');

        $this->walletObject->updateBalance();
    }

    public function getTargetNodeObject()
    {
        return LnNode::find()->where(['id'=>$this->target_ln_node_id,'user_id'=>\LNPay::$app->user->id])->one();
    }

    public function checkNode()
    {
        if (!$this->targetNodeObject)
            throw new \yii\web\ServerErrorHttpException('Unknown node, cannot continue!');
    }

    /**
     * Transfer the balance to new node
     */
    private function _transferBalance()
    {
        try {
            $payment_options = [];
            $payment_options['fee_limit_msat'] = 10000;

            $rpcConnector = $this->walletObject->lnNode->getLndConnector('RPC');
            $result = $rpcConnector->keysend($this->targetNodeObject->default_pubkey,$this->walletObject->balance,[],$payment_options);
            return $result;
        } catch (\Throwable $t) {

            $this->addError('transfer_balance',$t->getMessage());
            return false;
        }
    }

    public function switchWalletTargetNode()
    {
        $transferBalanceResult = NULL;
        if ($this->transfer_balance) { //attempt to transfer balance
            if ($transferBalanceResult = $this->_transferBalance()) {
                unset($transferBalanceResult['htlcs']); // unnecessary for now, maybe add back later
                //hooray
            } else {
                return false;
            }
        }
        $walletObject = $this->walletObject;
        $walletObject->ln_node_id = $this->target_ln_node_id;
        if (!$walletObject->save()) {
            $this->addError('target_ln_node_id',HelperComponent::getFirstErrorFromFailedValidation($walletObject));
            return false;
        }

        $this->walletObject->user->registerAction(ActionName::WALLET_CHANGE_NODE,
            ArrayHelper::merge($this->attributes,[
                'keysendResultObject'=>$transferBalanceResult
            ],[])
        );
        return true;
    }

}
