<?
use yii\widgets\Breadcrumbs;
use common\components\SiteHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\widgets\Pjax;

Pjax::begin();
?>
    <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="relocateModal" id="relocateModal">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content" id="relocateModalBody">

            </div>
        </div>
    </div>
<?
echo Breadcrumbs::widget([
    'itemTemplate' => "<li>{link}</li>\n", // template for all links
    'links' => [
        [
            'label' => 'Опорная сеть: ARP',
            'url' => '/ipmon/backbone',
            'template' => "<li>{link}</li>\n", // template for this link only
        ],

        long2ip($list['nodeIp']),
    ],
]);

if(isset($list['post']['static'])){
    $active_vlan = $list['post']['static']['vlan'];
}else{
    if(isset($list['post']['BackboneHosts'])){
        $active_vlan = $list['post']['BackboneHosts']['vlan'];
    }else{
        $active_vlan = 0;
    }
}

if(!is_null($relocate_vlan)){
    $active_vlan=$relocate_vlan;
}


$JS=<<<JS
$('.relocate').on('click',function () {
            loader(true);
            var modal = $("#relocateModal");
        	var data = $.parseJSON($(this).attr('data-relocate'));
            modal.modal({
                backdrop: 'static',
                keyboard: false
            });
            modal.modal('show');
            $('#relocateModalBody').empty();
            $.ajax({
                type: "post",
                url: "/ipmon/backbone/relocate",
                data:{
                    'mac':data.mac,
					'node':data.node,
                    'ip':data.ip,
                    'vlan':data.vlan,
                },
                // dataType:'json',
                // async:false,
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    console.log(textStatus);
                },
                success: function(result){
                    $('#relocateModalBody').html(result);
                    loader(false)
                }
            });
            // relocateModal
        })
JS;
$this->registerJS($JS);

$arp = $list['arp'];
$node = $list['node'];

$excluded_vlan = $arp['excluded_vlan'];
?>

<? if($arp['status']){?>
    <? if($permissions['vlan']>1){ ?>
        <div class="btn-group">
            <button id="btnPin" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-h"></i></button>
            <ul class="dropdown-menu">
                <li><a href="/ipmon/backbone/node/<?=$node?>/vlan">Добавить VLAN</a></li>
            </ul>
            <!--            <button id="btnUnpin" class="btn btn-default btn-xs" disabled="disabled">Unpin</button>-->
        </div>
        &nbsp;<br>
        &nbsp;<br>
    <? } ?>
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="false">
    <?
        $vlan_count = 0;
        foreach ($arp['data'] as $vlan => $vlanData) { ?>
        <? if (isset($vlanData['destroy_date'])) {
            $umounted = true;
            $panel = "warning";
        } else {
            $umounted = false;
            $panel = "default";
        }

        if($vlan!=$excluded_vlan){
            $vlan_count++;
            ?><div class="panel panel-<?= $panel ?>">
            <div class="panel-heading" role="tab" id="heading<?=$vlan?>">
                <h4 class="panel-title">
                    <a role="button"  data-toggle="collapse" data-parent="#accordion" href="#collapse<?=$vlan?>" aria-expanded="true" aria-controls="collapse<?=$vlan?>"  class="nodecor">
                        <span class="badge" style="background: cornflowerblue"> vlan <?=$vlan?></span>
                        &nbsp;<? if (isset($vlanData['description'])) {
                            echo $vlanData['network'];
                        }
                        ?></a>&nbsp;
                    <span class="pull-right">
                        <? if (isset($vlanData['id'])) { ?>
                            <? if (!$umounted) {
                                ?><span class="badge" style="background: forestgreen">Free: <?= $vlanData['free'] ?></span>&nbsp;
                                <span class="badge" style="background: coral">Static: <?= $vlanData['static'] ?></span>&nbsp;
                                <span class="badge" style="background: grey">Dynamic: <?= $vlanData['dynamic'] ?></span>
                            <? } else {
                                ?><span class="badge" style="background: grey">
                                Hosts: <?= (int)$vlanData['dynamic']+(int)$vlanData['static'] ?></span><?
                            }
                            if($permissions['vlan']>1){
                                ?>&nbsp;<a href="/ipmon/backbone/node/<?=$node?>/vlan/<?=$vlanData['id']?>" class="nodecor"><i class="fa fa-edit"></i></a><?
                            }
                            //                            ?><!--&nbsp;<a href="/ipmon/backbone/node/1/vlan/--><?//=$vlan?><!--/status" class="nodecor"><i class="fa fa-power-off"></i></a>--><?//
                        } else {
                            ?>&nbsp;<a href="/ipmon/backbone/node/<?=$node?>/vlan/<?=$vlan?>" class="nodecor"><i class="fa fa-plus-circle"></i></a><?
                        }
                        ?>
                    </span>
                    <i class="help-block"><?
                        if (isset($vlanData['description'])) {
                            echo $vlanData['description'];
                        }

                        if (isset($vlanData['destroy_date'])) {
                            echo " <small class='text-warning hidden-xs'> Разобран: " . $vlanData['destroy_date'] . "</small>";
                        } else {
                            if (isset($vlanData['create_date'])) {
                                echo " <small class='text-success hidden-xs'> Создан: " . $vlanData['create_date'] . "</small>";
                            }
                        }
                        ?>
                    </i>
                </h4>
            </div>

            <div id="collapse<?=$vlan?>" class="panel-collapse collapse <? if($vlan==$active_vlan){echo 'in'; } ?>" role="tabpanel" aria-labelledby="heading<?=$vlan?>">
                <table class="table table-hover table-condensed">
                    <?

                    $i = 0;
                    $u = [0,0];

                    foreach ($vlanData['hosts'] as $ip => $host){

                        //Define variables
                        $i++;
                        $nullIP = (($ip+2)-key($vlanData['hosts']))%256;
                        $checked = "";
                        $description = "";
                        $row_style = 'active';
                        $disable_checkbox = true;
                        $icon = 'unlock';
                        $badge_color = "ulock_color";
                        $color = '0aa684';
                        $reloc = false;

                        //Conditions
                        if(!empty($host)){
                            $row_style = 'warning';
                            $u++;
                            $mac = $host['mac'];
                            if(isset($host['description'])){
                                $description = $host['description'];
                            }

                            if(isset($host['type'])){
                                if($host['type']==4){
                                    $checked = 'checked';
                                    $row_style = 'danger';
                                    $color = '713691';
                                    $icon = 'lock';
                                    $badge_color = "lock_color";
                                    $reloc = true;
                                }
                            }else{
                                if((isset($host['active']))&&($host['active'])){
                                    $checked = "checked";
                                    $row_style = 'info';
                                    $color = '713691';
                                    $icon = 'lock';
                                    $badge_color = "lock_color";
                                    $reloc = true;
                                }
                            }
                        }else{
                            if(!$u[0]){
                                $u=[1,$i];
                            }
                            $mac = "-";
                            $disable_checkbox = false;
                        }

                        if(($nullIP == 0)||($nullIP == 1)) {
                            $row_style = '';
                            $badge_color = "reserv_color";
                            $disable_checkbox = false;
                        }

                        if(!isset($vlanData['id'])){
                            $disable_checkbox = false;
                        }
                        if(!isset($vlanData['id'])){
                            $disable_checkbox = false;
                        }

                        if($u[1]==$i){ $row_style = 'success'; }

//
//                        SiteHelper::debug($host);
                        ?>

                        <tr class="<?=$row_style?>">
                            <td style="width: 30px!important;"><?=$i?></td>
                            <td style="width: 40px!important;">
                                <? if ($disable_checkbox){ ?>
                                    <? $form = ActiveForm::begin([
                                                'id' => 'ch'.$ip,
                                                'method' => 'post',
                                                'enableClientValidation' => false,
                                                'enableAjaxValidation' => true,
                                                'action'=>'/ipmon/backbone/node/'.$list['node'],
                                                'options' => ['data-pjax' => true]
                                                ]) ?>
                                    <input hidden <?=$checked?> name="BackboneHosts[active]" value="1" type="checkbox"  id="<?=$ip?>" onchange="if($(this).prop('checked')){
                                            $('#icon_<?=$ip?>').removeClass('fa-unlock').addClass('fa-lock');
                                            $('#badge_<?=$ip?>').removeClass('ulock_color').addClass('lock_color');
                                            //                                    $('#tr_<?//=$ip?>//').removeClass('success').addClass('danger');
                                            //                                    MakeStatic();
                                            }else{
                                            $('#icon_<?=$ip?>').addClass('fa-unlock').removeClass('fa-lock');
                                            $('#badge_<?=$ip?>').addClass('ulock_color').removeClass('lock_color');
                                    //                                    $('#tr_<?//=$ip?>//').addClass('success').removeClass('danger');
                                    //                                    RemoveStatic();
                                                                    };
                                            $('#ch<?=$ip?>').submit();
                                            ">
                                    <?
                                    ?>
                                    <input readonly hidden name="BackboneHosts[ip]" value="<?=long2ip($ip)?>">
                                    <input readonly hidden name="BackboneHosts[node]" value="<?=$node?>">
                                    <input readonly hidden name="BackboneHosts[mac]" value="<?=$mac?>">
                                    <input readonly hidden name="BackboneHosts[vlan]" value="<?=$vlan?>">
                                    <input readonly hidden name="BackboneHosts[vlan_id]" value="<?=$vlanData['id']?>">
                                    <input readonly hidden name="BackboneHosts[description]">
                                    <a onclick="$('#<?=$ip?>').click()" id="badge_<?=$ip?>" href="#" class="badge <?=$badge_color?>">
                                        <i id="icon_<?=$ip?>" class="fa fa-<?=$icon?> fa-fw"></i>
                                    </a>
                                    <? ActiveForm::end();
                                }
                                if($u[1]==$i){ ?>
                                    <span style="cursor: pointer" class="badge" onclick="$('#f<?=$ip?>').submit()"><i class="fa fa-plus fa-fw"></i></span>
                                <? } ?>
                            </td>
                            <td style="width: 40px!important;"><?=long2ip($ip)?></td>
                            <td style="width: 170px!important;">
                                <? if($u[1]==$i){
                                    $form = ActiveForm::begin([
                                        'id' => 'f'.$ip,
                                        'method' => 'post',
                                        'fieldConfig' => ['errorOptions' => ['encode' => false, 'class' => 'help-block']],
                                        'enableClientValidation' => false,
                                        'enableAjaxValidation' => true,
                                        'action'=>'/ipmon/backbone/node/'.$list['node'],
                                        'validationUrl' => '/ipmon/backbone/validate/lhost',
                                        'options' => ['data-pjax' => true,'enctype' => 'multipart/form-data']
                                    ]) ;?>
                                    <input readonly hidden name="BackboneHosts[ip]" value="<?=long2ip($ip)?>">
                                    <input readonly hidden name="BackboneHosts[description]" value="">
                                    <input readonly hidden name="BackboneHosts[vlan_id]" value="<?=$vlanData['id']?>">
                                    <input readonly hidden name="BackboneHosts[vlan]" value="<?=$vlan?>">
                                    <input readonly hidden name="BackboneHosts[active]" value="1">
                                    <input readonly hidden name="BackboneHosts[mount_date]" value="<??>">
                                <?= $form->field($model, 'mac',[
                                        'errorOptions' => [
                                            'encode' => false
                                        ]
                                    ])->textInput(['placeholder' => 'XX:XX:XX:XX:XX:XX','maxlength'=>17])->label(false) ?>
                                    <?ActiveForm::end();?>
                                <?  }else{
                                    echo $mac;
                                } ?>
                            </td>
                            <td><? if((((isset($host['type']))&&($host['type']==4))||((isset($host['active']))&&($host['active'])))&&(isset($vlanData['id']))){
                                    ?><a href="/ipmon/backbone/node/<?=$node?>/vlan/<?=$vlanData['id']?>/<?=$ip?>"><i class="fa fa-edit fa-fw"></i></a><?
                                } ?><?=$description?></td>
                            <td><?if($reloc){?>
                                <a href="#" class="relocate"
                                   data-relocate='{"ip":<?=$ip?>,"node":<?=$node?>,"mac":"<?=$mac?>","vlan":<?=$vlan?>}'
                                ><i class="fa fa-refresh"></i></a><?}?>
                            </td>
                            <td class="hidden-xs" style="width: 30px!important;"><? if ((isset($host['type']))&&(!isset($host['id']))){
                                ?><i class="fa fa-bolt"></i><?
                                }else{
                                ?>&nbsp;<?
                                }?></td>
                        </tr><?
                    }
?>
            </table>
        </div>
            <? } ?>
        <? }
        if(!$vlan_count){
            ?><div class="alert alert-warning" >
                <i class='fa fa-2x fa-exclamation pull-left'></i>
                <b class=' text-bold'>Нет неактивных VLANов</b>
                <small><a href="/ipmon/backbone/node/<?=$node?>/vlan">Можно добавить!</a></small>
                <br>
                <i class='text-muted'>В БД нет информации об VLANах на этом узле...</i>
            </div><?
        }?>
    </div>
  <?
}else{
    ?>
    <div class="alert alert-danger" >
        <i class='fa fa-2x fa-ban pull-left'></i>
        <b class=' text-bold'>Коммутатор не отвечает...</b>
        <small>Совсем!</small>
        <br>

    </div>


<? };
//SiteHelper::debug($list);
unset($list);



Pjax::end();