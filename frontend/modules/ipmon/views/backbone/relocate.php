<?php
use common\components\SiteHelper;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin([
    'id' => 'voip_form',
    'method' => 'post',
    'action'=>'/ipmon/backbone/node/'.$data['node'],
    'enableClientValidation' => false,
    'enableAjaxValidation' => true,
    'options' => ['data-pjax' => true]
]);?>
<div class="row">
    <div class="col-xs-12">
        <input hidden name="Relocate[ip]"   value="<?=$data['ip']?>">
        <input hidden name="Relocate[mac]"  value="<?=$data['mac']?>">
        <input hidden name="Relocate[node]" value="<?=$data['node']?>">
        <input hidden name="Relocate[vlan]" value="<?=$data['vlan']?>">
        <select onchange="console.log($(this).val())" class="form-control" name="Relocate[new_node]">
            <?foreach ($nodeList as $node){
                ?><option <? if($data['node']==$node['id']){
                    echo 'selected';
                }?> value="<?=$node['id']?>"><?=$node['title']?> <?=$node['description']?></option><?
            }?>
        </select>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <button type="submit" onclick=" $('#relocateModal').modal('hide');" class="btn btn-xs btn-warning">Сохранить</button>
        <button type="button" class="pull-right btn btn-xs btn-default" data-dismiss="modal">Close</button>
    </div>
</div>
<?

ActiveForm::end();
//SiteHelper::debug($data);
//SiteHelper::debug($nodeList);