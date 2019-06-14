<?php
use yii\widgets\Breadcrumbs;
use common\components\SiteHelper;
use yii\bootstrap\Tabs;


$h1 = "Телефония";
use common\widgets\AttributesTree;
echo Breadcrumbs::widget([
    'itemTemplate' => "<li>{link}</li>\n", // template for all links
    'links' => [


        'Телефония',
    ],
]);
?>
<h1><?=$h1?></h1>
