<?php
use yii\widgets\Breadcrumbs;
use common\components\SiteHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\widgets\Pjax;

$h1 = "Опорная сеть: ARP";
use common\widgets\AttributesTree;
echo Breadcrumbs::widget([
    'itemTemplate' => "<li>{link}</li>\n", // template for all links
    'links' => [
        [
            'label' => 'ARP таблицы',
            'url' => '/ipmon/arptables',
            'template' => "<li>{link}</li>\n", // template for this link only
        ],

        long2ip($id)." (".$router['description'].")",

    ],
]);

ksort($subnetslist);// сортировка


?><a type="button" class="close" href="/ipmon/arptables"><span aria-hidden="true">&times;</span></a><br><?
if($timeout){
    echo "Timeout...";
}else {
    ?>
    <div class="panel-group networks" role="tablist" aria-multiselectable="true" id="networks">
    <? foreach ($subnetslist as $subnet) {
        ?>
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="<?= ($subnet['iface']) ?>">
                <h4 class="panel-title">
                    <a role="button" data-toggle="collapse" class="nodecor tabtoggle" data-parent="#networks"
                       href="#collapse<?= ($subnet['iface']) ?>" id="subnet<?= ($subnet['iface']) ?>" aria-controls="collapse<?= ($subnet['iface']) ?>">
                        <div class="row">
                            <div class="col-xs-2 col-lg-1"><span class="badge" style="background: cornflowerblue"><?= $subnet['vlan'] ?></span>
                            </div>
                            <div class="col-xs-4 col-lg-3"><div class=" pull-left"><?= ($subnet['subnet']) ?>/<small><?= ($subnet['mask']) ?></small> <small class="visible-lg"><?= ($subnet['iface']) ?></small></div></div>
                            <div class="col-xs-6 col-lg-6">
                                <span class="help-block "><?= $subnet['comment']; ?></span>
                                <span hidden class=" hidden-xs" id="counts<?= ($subnet['iface']) ?>"><br>
                                    <small id="count_free_<?= ($subnet['iface']) ?>" class="" style="color: forestgreen">Free: </small>&nbsp;
                                    <small id="count_static_<?= ($subnet['iface']) ?>" class=""  style="color: coral">Static: </small>&nbsp;
                                    <small id="count_dynamic_<?= ($subnet['iface']) ?>" class=""  style="color: grey">Dynamic: </small>
                                </span>
                            </div>
                        </div>
                    </a>
                    <!--                    --><?// if (isset($vlans[$subnet['iface']])){
                    //                        echo $vlans[$subnet['iface']]['vlan-id'];
                    //                    }//=$vlans[$subnet['iface']]['vlan-id']?>
                </h4>
            </div>

            <div id="collapse<?=$subnet['iface']?>" data-router='<?=$id?>' data-network="<?=$subnet['iface']?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading<?=$subnet['iface']?>">
                <div class="list-group" id="body<?=$subnet['iface']?>">
                    <li class="list-group-item text-center" ><i class="fa fa-spin fa-spinner fa-pulse fa-fw fa-2x text-info"></i></li>
                </div>
            </div>
        </div><?
    } ?>
    </div><?
    if($permissions['commenting']>0) {
        ?>
        <div class="modal fade" id="CommentChange" tabindex="-1" role="dialog" aria-labelledby="CommentChange">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form id="CommentForm">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="CommentChange">Коментарий </h4>
                        </div>
                        <div class="modal-body">

                            <div class="form-group">
                                <label for="ip" class="control-label">IP адрес:</label>
                                <input type="text" name="comment[ip]" class="form-control" id="ip" readonly>
                                <input hidden name="comment[router_id]" id="rid">
                                <input hidden name="comment[iface]" id="iface">
                            </div>

                            <div class="form-group">
                                <label for="comment" class="control-label"><i class="fa fa-edit fa-fw"></i> Коментарий:</label>
                                <textarea name="comment[comment]" class="form-control" id="comment"></textarea>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                            <button class="btn btn-primary" type="submit">Сохранить</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?
    }
//    SiteHelper::debug($subnetslist);
//    SiteHelper::debug($tmp);
//    $subnets = [];
}
//unset ($arp['addrs']['timeout']);

/*
?><a type="button" class="close" href="/ipmon/arptables"><span aria-hidden="true">&times;</span></a><br>
<div class="row">
<!--    <div class="col-xs-6">--><?// SiteHelper::debug($arp['vlans'])?><!--</div>-->

    <div class="panel-group networks" role="tablist" aria-multiselectable="true" id="networks">
        <? foreach ($arp['addrs'] as $subnet=>$subnetdata){
        ?><div class="panel panel-default">
            <div class="panel-heading" role="tab"  id="<?=$subnetdata['interface']?>">
                <h4 class="panel-title">
                    <a role="button" data-toggle="collapse" class="nodecor" data-parent="#networks" href="#collapse<?=$subnetdata['interface']?>" aria-controls="collapse<?=$subnetdata['interface']?>">
                        <b><?
                            if(isset($vlans[$subnetdata['interface']]['vlan-id'])){
                                echo $vlans[$subnetdata['interface']]['vlan-id'];
                            }else{
                                print_r($subnetdata);
                            }?><?/*
                            if(isset($datum['vlan'])) {
                                ?><b>&nbsp;
                                <span class="badge" style="background: cornflowerblue"> vlan <?=$datum['vlan']['vlan_id']?></span>
                                <span class="text-muted"><?=$datum['vlan']['comment']?></span>
                                </b>&nbsp;<?
                            }
                            echo $datum['range'][0].'/'.$datum['cidr'];*/
                            /*?></b>&nbsp;
                        <div class="pull-right">
                            <span  class="badge" style="background: forestgreen">Free: <?=$datum['FreeCount']?></span>&nbsp;
                            <span class="badge"  style="background: coral">Static: <?=$datum['StaticCount']?></span>&nbsp;
                            <span class="badge"  style="background: grey">Dynamic: <?=$datum['DynamicCount']?></span>
                        </div>
                        <i class="help-block"><?
                            if(isset($datum['comment'])) echo $datum['comment'];
                            *//*?></i>
                        </b>
                    </a>
                </h4>
            </div>
        </div><? }
    ?></div>
</div><?*/
/*if(!$arp['timeout']) {
    unset ($arp['timeout']);
    $i=0;
    ?><div class="panel-group networks" role="tablist" aria-multiselectable="true" id="networks"><?
    foreach ($arp as $net => $datum){
//                    foreach ($this->Networks($rid) as $net => $data){
        ?>
        <div class="panel panel-default">
            <div class="panel-heading" role="tab"  id="<?=$net?>">
                <h4 class="panel-title">
                    <a role="button" data-toggle="collapse" class="nodecor" data-parent="#networks" href="#collapse<?=$net?>" aria-controls="collapse<?=$net?>">
                        <b><?
                            if(isset($datum['vlan'])) {
                                ?><b>&nbsp;
                                <span class="badge" style="background: cornflowerblue"> vlan <?=$datum['vlan']['vlan_id']?></span>
                                <span class="text-muted"><?=$datum['vlan']['comment']?></span>
                                </b>&nbsp;<?
                            }
                            echo $datum['range'][0].'/'.$datum['cidr'];
                            ?></b>&nbsp;
                        <div class="pull-right">
                            <span  class="badge" style="background: forestgreen">Free: <?=$datum['FreeCount']?></span>&nbsp;
                            <span class="badge"  style="background: coral">Static: <?=$datum['StaticCount']?></span>&nbsp;
                            <span class="badge"  style="background: grey">Dynamic: <?=$datum['DynamicCount']?></span>
                        </div>
                        <i class="help-block"><?
                            if(isset($datum['comment'])) echo $datum['comment'];
                            ?></i>
                    </a>
                </h4>
            </div>
            <div id="collapse<?=$net?>" data-router='<?=$id?>' data-network="<?=$datum['interface']?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading<?=$net?>">
                <div class="list-group" id="body<?=$datum['interface']?>">
                    <li class="list-group-item text-center" ><i class="fa fa-spin fa-spinner fa-pulse fa-fw fa-2x text-info"></i></li>
                </div>
            </div>
        </div>
    <? } ?></div><?
}else{
    ?><div class="alert alert-warning" >
        <i class='fa fa-2x fa-warning pull-left'></i>
        <b class=' text-bold'>Request timeout</b>
        <small>Превышен интервал ожидания</small>
        <br>
        <i class='text-muted'>Вероятно у веб-сервера нет доступа до роутера, либо роутер не отвечал в течении 10 сек.</i>
    </div><?
}

//SiteHelper::debug($router);
//c$vlans[$j]['comment'], 'utf-8', mb_detect_encoding($vlans[$j]['comment'])),  //кодировка
//SiteHelper::debug($arp);*/?>
