<?php
use yii\widgets\Breadcrumbs;
use common\components\SiteHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\bootstrap\Progress;
//echo "Hello<br><pre>";
//print_r($test);
//echo "</pre>";
//Pjax::begin();
$h1 = "Опорная сеть";
use common\widgets\AttributesTree;
echo Breadcrumbs::widget([
    'itemTemplate' => "<li>{link}</li>\n", // template for all links
    'links' => [


        "Опорная сеть",
    ],
]);
//print_r($permissions);  //Array ( [change_state] => 2 [r_switches] => 2 )
$nodesId=[];
//echo Progress::widget([
//    'percent' => 0,
//    'barOptions' => ['class' => 'progress-bar-success'],
//    'options' => ['class' => 'active progress-striped']
//]);
?>

<div class="row">
        <div class="col-xs-12">
        <? if($permissions['r_switches']==2){ ?>
        <div class="btn-group">
            <button id="btnPin" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-h"></i></button>
            <ul class="dropdown-menu">
                <li><a href="/ipmon/backbone/editnode">Добавить узел</a></li>
            </ul>
<!--            <button id="btnUnpin" class="btn btn-default btn-xs" disabled="disabled">Unpin</button>-->
        </div>
        <? } ?>
<hr>
        <!-- Breadcrumb -->
            <?// print_r($list); die();
            if(isset($list['nodes'])){
            $nodes_list = $list['nodes'];
            ?>
        <div class="panel panel-default" >
            <table class="table table-hover table-condensed">
                <? for ($i = 0; $i < count($nodes_list); $i++) : ?>
                <? $nodesId[] = $nodes_list[$i]['id']?>
                    <tr class='<?= $nodes_list[$i]['row_class']?>'>
                        <td>
                            <a class='pull-left nodecor nodeItem' title='Просморт ARP' href='/ipmon/backbone/node/<?= $nodes_list[$i]['id']?>' id='<?= $nodes_list[$i]['id']?>'>
                                <div>
                                <?/*?><i class='<?=$nodes_list[$i]['icon']?>'></i><?*/?>
                                <i class='fa fa-pulse fa-spinner text-warning' id="icon_<?= $nodes_list[$i]['id']?>"></i>
                                <b class='list-group-item-heading text-bold'><?=$nodes_list[$i]['description']?></b>
                                <i class='list-group-item-text text-muted'><?=$nodes_list[$i]['title']?></i>&nbsp;
                                </div>
                                <div>
                                <small class="text-muted">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= strtoupper($nodes_list[$i]['mac'])?></small><?
                                /*if(isset($nodes_list[$i]['uptime'])){
                                ?><br>
                                <small class="text-warning bg-success">System Uptime: </small><small class="text-muted bg-success"><?=$nodes_list[$i]['uptime']?></small><?
                                }?> if(isset($nodes_list[$i]['uptime'])){
                                */?>
                                </div>
                            </a>
                        </td>
                        <td class="hidden-xs">

                        </td>
                        <td>
                            <div class="pull-right"><?
                                if(isset($nodes_list[$i]['snmp_model'])){ //Проверить
                                    ?><span class="label label-<?=$nodes_list[$i]['label'] ?>"><?=$list['snmp_models'][$nodes_list[$i]['snmp_model']]?></span><?
                                }else{
                                    ?><span class="label label-<?=$nodes_list[$i]['label'] ?>"><?=$list['models'][$nodes_list[$i]['model']]?></span><?
                                }?>
                            <br>
                                <small class="text-warning bg-success">System Uptime: </small>
                                <small class="text-muted bg-success"  id="uptime_<?= $nodes_list[$i]['id']?>">
                                    <i class='fa fa-pulse fa-spinner text-warning'></i>
                                </small>
                            </div>
                        </td>
                        <? if($permissions['r_switches']>1) : ?>
<!--                            <td style="width: 30px!important;"><a class='nodecor' title='Деактивировать'><i class='fa fa-power-off text-danger'></i></a></td>-->
                            <td style="width: 30px!important;"><a class='nodecor' href="/ipmon/backbone/editnode/<?=$nodes_list[$i]['id']?>" title='Редактировать'><i class='fa fa-edit'></i></a></td>
                        <? endif ?>
                    </tr>
                <? endfor; ?>
            </table>

        </div>
            <? }else{
                ?><div class="alert alert-warning" >
                    <i class='fa fa-2x fa-exclamation pull-left'></i>
                    <b class=' text-bold'>Нет активных узлов</b>
                    <small><a href="/ipmon/backbone/editnode">Можно добавить!</a></small>
                    <br>
                    <i class='text-muted'>В БД нет информации об узлах...</i>
                </div><?
            }

           /* if(0) {
//            if($permissions['not_active']>0) {
                ?>
                <hr>
                <? if ((isset($list['disabled']))&&(count($list['disabled'])>0)) {
                        $disabled_list = $list['disabled']; ?>
                        <div class="panel panel-default">

                            <table class="table table-hover table-condensed">
                                <? for ($i = 0; $i < count($disabled_list); $i++) : ?>
                                    <tr>
                                        <td>
                                            <i class="fa fa-ban fa-fw"></i>
                                            <b class='list-group-item-heading text-bold'><?= long2ip($disabled_list[$i]['ip']) ?></b>
                                            <small><?= $disabled_list[$i]['mac'] ?></small>
                                            <br>
                                            <i class='list-group-item-text text-muted'><?= $disabled_list[$i]['description'] ?></i>
                                        </td>
                                        <? if (($permissions['r_switches'] > 1) && ($permissions['not_active'] > 1)) : ?>
                                            <!--                                        <td style="width: 30px!important;"><a class='nodecor' title='Активировать'><i class='fa fa-power-off text-success'></i></a></td>-->
                                            <td style="width: 30px!important;"><a class='nodecor'
                                                                                  href="/ipmon/backbone/editnode/<?= $disabled_list[$i]['id'] ?>"
                                                                                  title='Редактировать'><i
                                                            class='fa fa-edit'></i></a></td>
                                        <? endif ?>
                                    </tr>
                                <? endfor; ?>
                            </table>

                        </div>
                    <? } else {
                        ?>
                        <div class="alert alert-warning">
                            <i class='fa fa-2x fa-exclamation pull-left'></i>
                            <b class=' text-bold'>Нет неактивных узлов</b>
                            <small><a href="/ipmon/backbone/editnode">Можно добавить!</a></small>
                            <br>
                            <i class='text-muted'>В БД нет информации об узлах...</i>
                        </div><?
                    }
            }*/?>
<!--        <hr>-->
<!--        <small>Accessed via <a href="http://www.gbif.org/dataset/d7dddbf4-2cf0-4f39-9b2a-bb099caae36c">GBIF Secretariat: GBIF Backbone Taxonomy</a> on Tue Oct 31 2017 07:51:32 GMT+0500 (+05).<br>Open <a href="http://www.gbif.org/species/8306914" target="_blank">Authorative Information on GBIF</a>.-->
<!--        </small>-->
    </div>
</div>

<?


//Pjax::end();

?>

