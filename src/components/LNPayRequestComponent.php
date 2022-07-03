<?php

namespace lnpay\components;
use Yii;

class LNPayRequestComponent extends \yii\web\Request
{
    /**
     * Returns the user IP address.
     * The IP is determined using headers and / or `$_SERVER` variables.
     * @return string|null user IP address, null if not available
     */
    public function getUserIP()
    {
        if ($forwarded = @$_SERVER['HTTP_X_FORWARDED_FOR']) {
            $ip = trim(@explode(",",$forwarded)[0]);
            if ($ip)
                return $ip;
        }

        return parent::getUserIp();
    }
}

?>