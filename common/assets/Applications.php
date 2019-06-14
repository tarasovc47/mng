<?php

namespace common\assets;

use yii\web\AssetBundle;

class Applications extends AssetBundle
{
	public $sourcePath = '@common';
    public $css = [
    	"css/applications/style.css",
   	];
    public $js = [
        "js/applications/script.js",
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
