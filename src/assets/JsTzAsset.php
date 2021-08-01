<?php

namespace lnpay\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class JsTzAsset extends AssetBundle
{
    public $sourcePath = '@npm/jstimezonedetect';
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
    public $css = [
    ];
    public $js = [
        'dist/jstz.min.js'
    ];
    public $depends = [
    ];
}
