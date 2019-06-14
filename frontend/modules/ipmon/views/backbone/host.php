<?php
use yii\widgets\Breadcrumbs;
use common\components\SiteHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;

Pjax::begin();
function is_in_str($str,$substr)
{
    $result = strpos ($str, $substr);
    if ($result === FALSE) // если это действительно FALSE, а не ноль, например
        return false;
    else
        return true;
}
echo Breadcrumbs::widget([
    'itemTemplate' => "<li>{link}</li>\n", // template for all links
    'links' => [
        [
            'label' => 'Опорная сеть: ARP',
            'url' => '/ipmon/backbone',
            'template' => "<li>{link}</li>\n", // template for this link only
        ],
        [
            'label' => long2ip($list['nodeIP']),
            'url' => '/ipmon/backbone/node/'.$list['node'],
            'template' => "<li>{link}</li>\n", // template for this link only
        ],
        long2ip($list['hostIP'])
    ],
]);

//echo $vlan."<br>";
//echo $list['node']."<br>";
//echo $host;
//print_r($list);
//if (count($list['db_hosts']) == 0) {
//    $title = "Добавить VLAN";
//    $readonly = false;
//} else {
//    $title = "Редактировать VLAN";

//    $vlanData = $list['db_hosts'][0];
//    $readonly = true;
//}
$readonly = [];
$model_exist = true;
if(isset($list['data']['sw_model'])){
    if(!is_in_str($list['models'][$list['data']['sw_model']],'ltex')){
        $model_exist = false;
    }
}

if(!isset($list['data']['configured'])){
    $list['data']['configured'] = '';
}
$model->configured = $list['data']['configured'];


?><div class="row">
    <div class="">
        <div class="col-md-8 col-sm-10 col-xs-12 col-lg-7">
            <? $form = ActiveForm::begin([
    'id' => 'NewVlan',
    'method' => 'post',
    'enableClientValidation' => false,
    'enableAjaxValidation' => true,
    'action'=>'/ipmon/backbone/node/'.$list['node'],
    'validationUrl' => '/ipmon/backbone/validate/host',
    'options' => ['data-pjax' => true]
]) ?>
<a type="button" class="close" href="/ipmon/backbone/node/<?= $list['node'] ?>"><span aria-hidden="true">&times;</span></a>
<h4 class="title">Коментарий по узлу</h4>
<hr>
<div class="row">
    <input name="BackboneHosts[node]" hidden value="<?= $list['node'] ?>">
    <? if(isset($list['vlanID'])){?><input name="BackboneHosts[vlan_id]" hidden value="<?= $list['vlanID'] ?>"> <? } ?>
    <div class="col-xs-4 col-md-6 col-lg-2"><?= $form->field($model, 'vlan')->input('number', ['readonly' => true, 'value' => $list['vlan']]) ?></div>

    <div class="col-xs-4 col-md-6 col-lg-3"><?= $form->field($model, 'ip')->input('search', ['readonly'=>true,'value'=>long2ip($list['hostIP'])]) ?></div>
    <div class="col-xs-4 col-md-6 col-lg-3"><?
        if(isset($list['data']['sw_model'])){
            $model->sw_model = $list['data']['sw_model'];
//            ?><!--<input hidden name="BackboneHosts[sw_model]" value="--><?//=$list['data']['sw_model']?><!--">--><?//
//            ?><!--<label class="control-label">Модель</label>--><?//
//            ?><!--<input class="form-control" readonly value="--><?//=$list['models'][$list['data']['sw_model']]?><!--">--><?//
        }
            echo $form->
            field($model, 'sw_model')->
            dropDownList(
                $list['models'],
                ['prompt' =>'Выберите модель...']
            );
//        }
        ?></div>
    <div class="col-xs-8 col-md-6 col-lg-4"><?= $form->field($model, 'mac')->textInput(['readonly'=>true,'placeholder' => 'XX:XX:XX:XX:XX:XX','maxlength'=>17,'value'=>$list['data']['mac']]) ?></div>


</div>
<div class="row">
    <div class="col-xs-12">
        <!--        --><?//= $form->field($model, 'description')->textarea(['value'=>$hostData['description']]) ?>
        <?= $form->field($model, 'description')->textarea(['value'=>$list['data']['description']]) ?>

<!--    <div class="col-xs-8">--><?//= $form->field($model, 'mount_date')->input('search', ['readonly'=>true,'value' => $hostData['mount_date'],'class'=>'form-control datepick']) ?><!-- </div>-->
<!--    <div class="col-xs-4">--><?//= $form->field($model, 'vlan_id')->input('number', ['readonly' => true, 'value' => $vlan]) ?><!--</div>-->
    </div>
</div>
<div class="row">
    <? $model->active = $list['data']['active'];?>
<!--    --><?// $model->configured = $list['data']['configured'];?>
    <div class="col-xs-6">
        <?= $form->field($model, 'active')->checkbox(['value'=>1, 'uncheckValue'=>0]) /// Здесь какой то глюк ?>
    </div>
    <div class="col-xs-6">

        <? if((!$list['data']['configured'])&&($permissions['config']>1)&&($model_exist)){ ?>
        <?= $form->field($model, 'configured')->checkbox(['value'=>1, 'uncheckValue'=>0]) /// Здесь какой то глюк ?>
        <? } ?>
    </div>
</div>


<hr>
            <div class="pull-right">
                <a href="/ipmon/backbone/node/<?= $list['node'] ?>" class="btn btn-default"
                   data-dismiss="modal">Закрыть</a>
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
            </div>
<? ActiveForm::end(); ?>
        </div>
    </div>
</div>
<?


Modal::begin([
    'header' => '<h4 class="text-danger">Внимание!!</h4>',
    'footer' => Html::button('Подтвердить', ["class" => "btn btn-success",'onclick'=>'$(\'#confirm\').modal(\'hide\');']) .
    Html::button('Отмена', ["class" => "btn btn-default cancel", "data-dismiss" => "modal", 'onclick'=>'$(\'#backbonehosts-configured\').removeAttr(\'checked\');']),
    'id' => 'confirm',
    'closeButton' => false,
    ]);
    ?>
    <div class="confirm-content"></div>
<?php Modal::end(); ?>

<!--    <a onclick="$('#confirm').modal('show');"> dddd</a>-->
<?php
$JS=<<<JS
$('#backbonehosts-sw_model').on('change',function() {
   if(!($(this).val().indexOf('ltex')+1)){
       $('#backbonehosts-configured').removeAttr('disabled');
   }else{
       $('#backbonehosts-configured').removeAttr('checked').attr('disabled','disabled');
   }
});

$('#backbonehosts-configured').on('change',function() {
            if ($(this).prop("checked")){
                $('#confirm').modal('show');
                $("#confirm .confirm-content").html("На уже настроенных коммутаторах это приведет к сбросу конфигурации. Подвердите действие");
                $('#backbonehosts-sw_model').attr('required','requred');
                console.log('check');
            }else{
                $('#backbonehosts-sw_model').removeAttr('required');
                console.log('uncheck');
            }
        });
JS;
$this->registerJS($JS);

//SiteHelper::debug($list);
Pjax::end();