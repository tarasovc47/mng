<?php
use yii\widgets\Breadcrumbs;
use common\components\SiteHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\widgets\Pjax;
use common\widgets\AttributesTree;

//Pjax::begin();
$h1 = "Опорная сеть: ARP";
//phpinfo();
//die();
echo Breadcrumbs::widget([
    'itemTemplate' => "<li>{link}</li>\n", // template for all links
    'links' => [
        "ARP таблицы",
    ],

]);
?>
    <div class="panel panel-default">
    <table class="table table-condensed table-hover"><?
foreach ($routers as $router){
    ?><tr id="tr<?=ip2long($router['ip'])?>">
        <td>
            <a class="nodecor tabtoggle" id="r<?=ip2long($router['ip'])?>" href="/ipmon/arptables/<?=ip2long($router['ip'])?>"><?=$router['description']?> <span class="text-warning">[<b><?=$router['ip']?></b>]</span></a>
        </td>
        <td>
            <span><?=$router['text']?></span>
        </td>
        <td>
            <?if($permissions['addrouter']>1){?><a href="/ipmon/arptables/<?=ip2long($router['ip'])?>/edit"><i class="fa fa-edit"></i></a><?}else{ echo "&nbsp";}?>
        </td>
    </tr><?
}
  ?></table>
  </div><?
if($permissions['addrouter']>1){
    ?><a class="btn btn-xs btn-default" href="/ipmon/arptables/0">New</a><?
}

//SiteHelper::debug($routers);
//Pjax::end();