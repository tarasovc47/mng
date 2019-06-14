<?php

namespace frontend\assets;

use yii\web\AssetBundle;

# Последний скрипт и последний файл стилей 
# всегда должен быть наш, чтобы переопределять
# то, что нам нужно и быть приоритетней.
# JS по умолчанию подключаются в конце страницы,
# оставим это так для увеличения производительности
class MulpipleAddressesFormWidgetAsset extends AssetBundle
{
	public $css = [
        'css/widgets/mulpipleAddressesForm/mulpipleAddressesForm.css',
    ];
    public $js = [
        'js/widgets/mulpipleAddressesForm/mulpipleAddressesForm.js',
    ];
    public $depends = [
        'frontend\assets\AppAsset',
    ];
}
