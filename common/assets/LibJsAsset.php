<?php

namespace common\assets;

use yii\web\AssetBundle;

class LibJsAsset extends AssetBundle
{
    public $sourcePath = '@common';
    public $css = [
        'css/font-awesome/font-awesome.min.css',
        'css/chosen/chosen.css',
        'css/toggle/bootstrap-toggle.min.css',
        'css/datetimepicker/bootstrap-datetimepicker.css',
    ];
    public $js = [
        'js/chosen/chosen.jquery.js',
        'js/jquery.cookie.js',
        'js/toggle/bootstrap-toggle.min.js',
        'js/moment/moment.js',
        'js/moment/ru.js',
        'js/datetimepicker/bootstrap-datetimepicker.js',
        'js/lib.js',
    ];
}