<?php
namespace lnpay\components;

class LNPayUrlManager extends \yii\web\UrlManager
{
    public function createAbsoluteUrl($params, $scheme = null)
    {
        return parent::createAbsoluteUrl($params,YII_ENV_PROD?'https':'http');
    }

}

