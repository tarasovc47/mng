<?php
use yii\helpers\Html;
use common\components\SiteHelper;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
$this->params['breadcrumbs'][] = ['label' => 'Инструменты'];
$this->params['breadcrumbs'][] = ['label' => 'Голосовые шлюзы'];

$servertime=time();

$dateserv=date("Y-m-d H:i:s", time());
$dateserv2=new DateTime($dateserv);
//SiteHelper::debug($gates);
$current_mac = '';
?><style>
    .nodecor {
        text-decoration: none!important;
    }
</style><?php if (Yii::$app->session->hasFlash('mac')){
    foreach(Yii::$app->session->getAllFlashes() as $type => $messages){


        if($type=='mac'){
            $el = "'#l".$messages."'";
            $JS=<<<JS
            $($el).click();
JS;
            $this->registerJS($JS);
        }
    }
}
//Pjax::begin();
//SiteHelper::debug($voip_gates);
$valueIP = '';
if(isset($_GET['ip'])){
    $valueIP = $_GET['ip'];
}
?>

            <?$form2 = ActiveForm::begin([
            'id' => 'voip_form',
            'method' => 'post',
            'action'=>'/tools/voip',
//            'enableClientValidation' => false,
//            'enableAjaxValidation' => true,
//            'validationUrl' => '/tools/voip/validate',
//            'options' => ['data-pjax' => true]
            ]);?>
            <table class="table">
                <tr>
                    <td style="width: 30px!important;" class=" pull-right">
                        <button class="btn btn-default" type="submit">
                            <i class="fa fa-search"></i>
                        </button>
                    </td>
                    <td>
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <?= $form2->field($model,'ip')->textInput(['placeholder'=>'IP адрес или подсеть','value'=>$valueIP])->label(false)?>
                        </div>
                    </td>
                </tr>
            </table>
            <?ActiveForm::end();?>
    <div class="panel-group" id="voipgates" role="tablist" aria-multiselectable="true"><?

if(!empty($gates)){
//    SiteHelper::debug($gates);
//    die();
    foreach( $gates as $key=>$val){
        $diff_time = $servertime - $val['last'];
        $icon = 'rocket';
        $class = 'default';
        $color = '#28a745';

        if($diff_time>1800){
            $class = 'warning';
            $icon = 'warning';
            $color = '#dc3545';
        }
        if($diff_time>86400){
            $class = 'danger';
            $icon = 'ban';
            $color = '#dc3545';
        }

        $sys_mac = str_replace(":","",$val['mac']);
        ?><div class="panel panel-<?=$class?>">
            <div class="panel-heading" role="tab" id="<?=$key?>">
                    <div class="row">
                        <div class="col-sm-8">
                            <h4 class="panel-title">
                               <a role="button" <?if(isset($voip_gates[$val['url']])){?>data-toggle="collapse" href="#<?=$sys_mac?>"<?}?> class="nodecor" data-parent="#voipgates"  id="l<?=$sys_mac?>" aria-expanded="true" aria-controls="<?=$sys_mac?>">

                                   <i class="fa fa-fw fa-<?=$icon?>"></i>
                                    <b class=""><?=$val['url']?></b>
                                   &nbsp;

                                   <span class="label label-<?if(isset($voip_gates[$val['url']])){
                                        echo 'success';
                                   }else{
                                        echo "warning";
                                   }?>">
                                   <?=$val['product']?>   <?
                                   $p=0;
                                   if(count($val['m'])==2){
                                       $p=1;
                                   }
                                   ?></span>&nbsp;<?
                                   if(isset($voip_gates[$val['url']])){ ?> <b><span class="text-primary"><?= $voip_gates[$val['url']]['description'] ?> </span></b> <?}
                                   foreach ($val['m'] as $portn){

                                       $portclass = "#6c757d";
                                       if($portn){
                                           $portclass="#28a745";
                                       }
                                       ?><span class="badge" style="background: <?=$portclass?>"><?=$p?></span> <?
                                       $p++;
                                   }?>
                                   <br>
                                   <small class="text-muted">
                                      <i class="fa fa-fw fa-circle" style="color: <?=$color?>"></i> <?=SiteHelper::secondsToTime($diff_time)?> |
                                      <?=$val['mac']?> <b><span class="text-primary">S/N: <?=$key?></span></b>
                                   </small>
                               </a>
                            </h4>
                        </div>
                        <div  class="col-sm-4">
                            <?if(!isset($voip_gates[$val['url']])){
                                $form = ActiveForm::begin([
                                    'id' => 'accounting_form',
                                    'method' => 'post',
                                    'action'=>'/tools/voip/gate/'
//                            'enableClientValidation' => false,
//                            'enableAjaxValidation' => true,
//                            'validationUrl' => '/sessions/validate',
//                            'options' => ['data-pjax' => true]
                                ]);?>
                            <div class="">
                                <div class="input-group">
                                    <input name="addGate[description]" placeholder="Примечание" class="form-control">
                                    <span class="input-group-btn">
                                            <button class="btn btn-default" type="submit"><i class="fa fa-fw fa-lock"></i></button>
                                          </span>
                                </div><!-- /input-group -->
                            </div>
                            <input name="addGate[mac]" hidden value="<?=$sys_mac?>">
                            <input name="addGate[ip]" hidden value="<?=$val['url']?>">
                            <?
                            ActiveForm::end();
                            }?>
                        </div>
                </div>
            </div>
            <?if($permissions['edit']>0){
                ?><div id="<?=$sys_mac?>" class="panel-collapse collapse" data-devtype="<?=$val['product']?>" data-db-id="<? if(isset($voip_gates[$val['url']]['id'])){ echo $voip_gates[$val['url']]['id'];}?>" data-ip="<?=$val['url']?>" role="tabpanel" aria-labelledby="heading<?=$key?>">
                <div class="panel-body" id="body<?=$sys_mac?>"></div>
            </div><?}?>
        </div>
        <?php
    }
}
?></div>
<?
$JS = <<<JS
var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};

function ClickDiv(varsa) {
    console.log(varsa);
}


var perc_of_digits = 0.3;
function randomInRange(minVal,maxVal)
{
    var randVal = minVal+(Math.random()*(maxVal-minVal));
    return Math.round(randVal)
}

function getCharInRange(from,till){
    ret = String.fromCharCode( randomInRange(from,till) );
    return ret
}

function genPasswd(passwd_len){
    var ret = '';
    var first_iter = 1;
    while( passwd_len > 0 ){

        if(!first_iter && Math.random() < perc_of_digits ){
            ret+= getCharInRange(48,57)
        }
        else{
            if( Math.random() > 0.5 ){
                // upper
                ret+= getCharInRange(65,90)
            }
            else{
                // lower
                ret+= getCharInRange(97,122)
            }
        }

        first_iter = 0;
        passwd_len--;
    }
    return ret
}

function setPasswd(obj_id){
    var set_obj = document.getElementById(obj_id);
    set_obj.value = genPasswd(20)
}

function Changed(id){
        $('#changed' + $('input[data-port-id=' + id + ']').attr('data-port-id')).val('1');
}

function TrigContext(id) {
    if($('#state' + id).prop('checked')) {
        $('#context' + id).removeAttr("disabled");
    }else{
        // $('#context' + id).attr("disabled","disabled");
    }

}
function Deleting(id) {
    console.log(id);
    if($('#del' + id).prop('checked')) {
        $('#tr' + id).removeClass("success").addClass('danger');
    }else{
        $('#tr' + id).removeClass("danger").addClass('success');
    }

}

$(document).ready(function (e) {

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });
    $('#l' + getUrlParameter('mac')).click();


    $('#voipgates').on('shown.bs.collapse', function () {
        loader(true);
        var mac = $('.collapse.in').attr('id');
        var devtype = $('.collapse.in').attr('data-devtype');
        var dbID = $('.collapse.in').attr('data-db-id');
        var ip = $('.collapse.in').attr('data-ip');
        console.log(mac);

        $.ajax({
            type: "post",
            url: "/tools/voip/device",
            data:{
                'mac':mac,
                'devtype':devtype,
                'deviceId':dbID,
                'ip':ip,
            },
            // dataType:'json',
            // async:false,
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log(textStatus);
            },
            success: function(result){
                $("#body" + mac).html(result);
                loader(false);
            }
        });

        // GetArps(net,rid);
    }).on('show.bs.collapse', function () {
        $('.list-group').html('<li class="list-group-item text-center" ><i class="fa fa-spin fa-spinner fa-pulse fa-fw fa-2x text-info"></i></li>');
    });


    // $('#voipgates').on('hidden.bs.collapse', function () {
    //     console.log($('.collapse .in').attr('id'));
    //
    // })


});
JS;
//$this->registerJS($JS);

//Pjax::end();
//SiteHelper::debug($voip_gates);
/*Кусок пример для работы с гидрой*/

/*
$username = "asmild";
$password = "fRWH";
$host_api = "https://iibill.t72.ru:18020";
$param = "subjects/customers/302";

// авторизация
$curl = curl_init($host_api);
curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($curl, CURLOPT_USERPWD, $username . ":" . $password);
// get запрос
curl_setopt($curl, CURLOPT_URL, "$host_api/rest/v1/$param");
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($curl);
// вывести результат
SiteHelper::debug(json_decode($result));*/