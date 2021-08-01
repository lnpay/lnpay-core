<?php

namespace lnpay\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class PaywallAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
    public $css = [
        'css/site.css'
    ];
    public $js = [
        '/js/functions.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset'
    ];
}