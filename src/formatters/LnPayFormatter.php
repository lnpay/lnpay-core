<?php
namespace lnpay\formatters;

class LnPayFormatter extends \yii\i18n\Formatter
{
    public function asSats($value)
    {
        return $value.' sats';
    }

    public function asSatsUnlimited($value)
    {
        if ($value==0 || $value==NULL)
            return 'Unlimited';

        return $value.' sats';
    }

    public function asSeconds($value)
    {
        return $value.' seconds';
    }
}

?>