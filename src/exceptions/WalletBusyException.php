<?php
namespace lnpay\exceptions;

class WalletBusyException extends \yii\web\ServerErrorHttpException
{
    /**
     * Constructor.
     *
     * @param string     $path
     * @param int        $code
     * @param \Exception $previous
     */
    public function __construct($wallet_id, $code = 0, \yii\base\Exception $previous = null)
    {
        parent::__construct('Please try again soon, wallet busy ID:' . $wallet_id, $code, $previous);
    }
}