<?php

namespace lnpay\wallet\controllers\pub;

use lnpay\base\DashController;
use lnpay\components\HelperComponent;
use lnpay\wallet\models\LnWalletDepositForm;
use lnpay\wallet\models\LnWalletWithdrawForm;
use lnpay\wallet\models\Wallet;
use lnpay\wallet\models\WalletTransferForm;
use yii\web\Controller;

/**
 * WalletController implements the CRUD actions for Wallet model.
 */
class IndexController extends Controller
{
    /**
     * @param $keyPostfix wakewa = prefix, random characters = postfix
     */
    public function actionView($access_key)
    {
        $this->layout = '@app/views/layouts/public';

        $wallet = Wallet::findByKey($access_key);
        if (!$wallet) {
            return $this->render('invalid-wallet');
        }

        $wModel = new LnWalletWithdrawForm();
        $dModel = new LnWalletDepositForm();

        //Handle Withdrawal
        if (\LNPay::$app->request->isPost) {
            if ($wModel->load(\LNPay::$app->request->post())) {
                if (\LNPay::$app->session->getFlash('bt')) {
                    \LNPay::$app->session->setFlash('success', 'Withdrawal Sent!');
                    //return $this->refresh();
                }
            }
        }

        return $this->render('index',[
            'wallet'=>$wallet,
            'wModel' => $wModel,
            'dModel' => $dModel,
        ]);
    }
}