<?php

namespace common\assets;

use yii\web\AssetBundle;

# Последний скрипт и последний файл стилей 
# всегда должен быть наш, чтобы переопределять
# то, что нам нужно и быть приоритетней.
# JS по умолчанию подключаются в конце страницы,
# оставим это так для увеличения производительности
class AddressSearchWidgetAsset extends AssetBundle
{
    public $css = [
        'https://api.t72.ru/static/css/select2.min.css',
        'https://api.t72.ru/static/css/select2-bootstrap.min.css',
        'https://api.t72.ru/static/css/bootstrap-editable.css',
    ];
    public $js = [
        'https://api.t72.ru/static/js/tether.min.js',
        'https://api.t72.ru/static/js/select2.min.js',
        'https://api.t72.ru/static/js/i18n/ru.js',
        'https://api.t72.ru/static/js/bootstrap-editable.min.js',
        'https://api.t72.ru/static/js/fias-in-place.js',
        'https://api.t72.ru/static/js/fias-editable.js',
        'https://api.t72.ru/static/js/place-editable.js',
        'https://api.t72.ru/static/js/place-find-editable.js',
    ];
    public $depends = [
        'yii\web\YiiAsset', // yii.js, jquery.js
        'yii\bootstrap\BootstrapAsset', // bootstrap.css
        'yii\bootstrap\BootstrapPluginAsset', // bootstrap.js
        'yii\jui\JuiAsset',
    ];
}
