<?
use yii\widgets\Breadcrumbs;
use common\components\SiteHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
Pjax::begin();
echo Breadcrumbs::widget([
    'itemTemplate' => "<li>{link}</li>\n", // template for all links
    'links' => [
        [
            'label' => 'Опорная сеть: ARP',
            'url' => '/ipmon/backbone',
            'template' => "<li>{link}</li>\n", // template for this link only
        ],

        "Коммутатор",
    ],
]);


$modelsList =  [];
foreach ($node_model['models'] as $item=> $idata) {
    $modelsList[$idata['id']] = $idata['vendor'].' '.$idata['model'];
}

if(!isset($nodeData->id)){
    $title='Добавить коммутатор';
    $nodeData['ip']= null;
    $nodeData['community']= 'sysadmin';
    $nodeData['mount_date']  = date("Y-m-d H:i");
    $nodeData['man_vlan']= '2221';
    $nodeData['ipmon_id']= '';
    $nodeData['active']= 1;
    $nodeData['description']= null;
    $nodeData['mac']= null;
    $nodeData['node_model']= null;
    $readonly = false;
}else{

    $currentModel = $node_model['models'][trim($node_model['snmp'])];
    $title='Редактировать коммутатор';
    $nodeData['ip']=long2ip($nodeData['ip']);
    $readonly = true;
    if($nodeData['node_model']!=$currentModel['id']){
        ?><div class="alert alert-danger">Модель коммуатора (<?=$currentModel['vendor'].' '.$currentModel['model']?>) не совпадает с данными в БД</div><?
    }

}
?><div class="row">
    <div class="col-xs-12 col-md-8 col-lg-6">
        <? $form = ActiveForm::begin([
            'method' => 'post',
            'options' => ['data-pjax' => true],
            'id'=>'NewNode',
            'enableClientValidation'=>false,
            'enableAjaxValidation' => true,
//            'action'=>'/ipmon/backbone/',
            'validationUrl' => '/ipmon/backbone/validate/node',
        ]) ?>
            <a type="button" class="close" href="/ipmon/backbone"><span aria-hidden="true">&times;</span></a>
            <h4 class="title" id="myModalLabel"><?=$title?></h4>
        <hr>
            <div class="row">
                <div class="col-xs-4"><?= $form->field($model, 'ip')->input('search', ['value'=>$nodeData['ip']]) ?></div>
                <div class="col-xs-4"><?
//                if($nodeData['node_model']==""){
                    $model->node_model = $nodeData['node_model'];
                    echo   $form->field($model, 'node_model')->
                    dropDownList(
                        $modelsList,
                        ['prompt' =>'Выберите модель...']
                    );
//                }else{
//                    ?><!--<input hidden name="BackboneNodes[node_model]" value="--><?//=$nodeData['node_model']?><!--">--><?//
//                    ?><!--<label class="control-label">Модель</label>--><?//
//                    ?><!--<input class="form-control" readonly value="--><?//=$modelsList[$nodeData['node_model']]?><!--">--><?//
//                }

                    ?></div>
                <div class="col-xs-4"><?= $form->field($model, 'mac')->textInput(['placeholder' => 'XX:XX:XX:XX:XX:XX','maxlength'=>17,'value'=>$nodeData['mac']]) ?></div>
            </div>
            <div class="row">
                <div class="col-xs-4"><?= $form->field($model, 'community')->input('search', ['value'=>$nodeData['community']]) ?></div>
                <div class="col-xs-8"><?= $form->field($model, 'mount_date')->input('search', ['readonly'=>$readonly,'value'=>$nodeData['mount_date']]) ?> </div>
            </div>

            <?= $form->field($model, 'description')->textarea(['value'=>$nodeData['description']]); ?>

            <div class="row">
                <div class="col-xs-6"><?= $form->field($model, 'man_vlan')->input( 'number',['value'=>$nodeData['man_vlan']]) ?></div>
                <div class="col-xs-6"><?= $form->field($model, 'ipmon_id')->input( 'number',['value'=>$nodeData['ipmon_id']]) ?></div>

            </div>
            <? $model->active = $nodeData['active'];?>
            <?= $form->field($model, 'active')->checkbox(['value'=>1, 'uncheckValue'=>0]) /// Здесь какой то глюк?>
        <hr>
        <?if($permissions['replace']>1){?>
        <span class="btn btn-warning" onclick="$('#confirm').modal('show')"><i class="fa fa-refresh"></i></span>
        <?}?>
        <div class="pull-right">

            <a href="/ipmon/backbone" class="btn btn-default" >Закрыть</a>

            <?= Html::submitButton('Сохранить',['class'=>'btn btn-primary'])?>
        </div>
        <? ActiveForm::end();?>
    </div>
</div>
<?
Modal::begin([
    'header' => '<h4>Внимание!!</h4>',
    'footer' => Html::button('Подтвердить', ["class" => "btn btn-success",'onclick'=>'$(\'#confirm\').modal(\'hide\');']) .
        Html::button('Отмена', ["class" => "btn btn-default cancel", "data-dismiss" => "modal"]),
    'id' => 'confirm',
    'closeButton' => false,
]);
?>
<div class="confirm-content">Заменить коммуататор с сохранением всех узлов? (экспериментальная функция)</div>
<?php Modal::end();
//SiteHelper::debug($permissions);
Pjax::end();