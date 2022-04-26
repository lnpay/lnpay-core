<?php

namespace lnpay\wallet\controllers\pub;

use lnpay\base\DashController;
use lnpay\wallet\models\Wallet;
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
        return $this->render('index',compact('wallet'));
    }
}