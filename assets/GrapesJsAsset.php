<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main asset bundle.
 */
class GrapesJsAsset extends AssetBundle
{
    public $sourcePath = '@npm/grapesjs-preset-newsletter';
    public $css = [
        'dist/grapesjs-preset-newsletter.css',
        '//unpkg.com/grapesjs@0.10.7/dist/css/grapes.min.css',
        'style/material.css',
        //'style/tooltip.css',
        'style/toastr.min.css'
    ];
    public $js = [
        '//unpkg.com/grapesjs@0.10.7/dist/grapes.min.js',
        'dist/grapesjs-preset-newsletter.min.js',
        'https://unpkg.com/grapesjs-tooltip@0.1.5/dist/grapesjs-tooltip.min.js'
        //'js/toastr.min.js',
        //'private/ajaxable.min.js'
    ];
    public $depends = [
        'yii\web\YiiAsset'
    ];
}