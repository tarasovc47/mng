<?php
use yii\widgets\Breadcrumbs;
use common\components\SiteHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\widgets\Pjax;

Pjax::begin();

echo Breadcrumbs::widget([
    'itemTemplate' => "<li>{link}</li>\n", // template for all links
    'links' => [
        [
            'label' => 'Опорная сеть: ARP',
            'url' => '/ipmon/backbone',
            'template' => "<li>{link}</li>\n", // template for this link only
        ],
        [
            'label' => long2ip($list['nodeIp']),
            'url' => '/ipmon/backbone/node/'.$list['node'],
            'template' => "<li>{link}</li>\n", // template for this link only
        ],

        $vlan,
    ],
]);

//if(isset($_POST['BackboneVlans'])){
//    SiteHelper::debug($_POST);
//}else


        $vlanData = [
            'description' => '',
            'active' => 'checked',
            'create_date' => date("Y-m-d H:i"), //2017-10-11 12:01:00
            'network' => ''
        ];


        $checkboxOption = ['selected' => true];
        if ((count($list['db_vlan']) == 0)||($list['db_vlan']==0)) {
            $title = "Добавить VLAN";
            $readonly = false;
        } else {
            $title = "Редактировать VLAN";

            $vlanData = $list['db_vlan'][0];
            $vlan = $vlanData['vlan'];
            $readonly = true;
        }

        $new = false;
        if($list['db_vlan']==0){
            $new = true;
            $readonly = true;
        }


    ?>
    <div class="row">
    <div class="">

        <div class="col-md-8 col-xs-12 col-lg-6">
            <? $form = ActiveForm::begin([
                'id' => 'NewVlan',
                'method' => 'post',
                'enableClientValidation' => false,
                'enableAjaxValidation' => true,
                'action'=>'/ipmon/backbone/node/'.$list['node'],
                'validationUrl' => '/ipmon/backbone/validate/vlan',
                'options' => ['data-pjax' => true]
            ]) ?>
            <input name="BackboneVlans[count]" hidden value="<?= count($list['db_vlan']) ?>">
            <a type="button" class="close" href="/ipmon/backbone/node/<?= $list['node'] ?>"><span aria-hidden="true">&times;</span></a>
            <h4 class="title"><?= $title ?></h4>
            <hr>
            <div class="row">
                <div class="col-xs-4"><?
                    if(!$new){
                        echo $form->field($model, 'vlan')->input('number', ['readonly' => true, 'value' => $vlan]);
                    }else{
                        echo $form->field($model, 'vlan')->input('number', ['max'=>3999,'maxLength'=>4]);
                    } ?></div>

                <div class="col-xs-8"><?= $form
                        ->field($model, 'network')
                        ->input('search', [
                                'placeholder' => 'A.B.C.D/M',
                                'value'=>$vlanData['network']
                            ]) ?></div>
                <!--                <div class="col-xs-6">--><?//= $form->field($model, 'mask')
                ?><!--</div>-->
            </div>
            <div class="row">

                <!--                <div class="col-xs-4">-->
                <?//= $form->field($model, 'community')->input('search', ['value'=>'sysadmin'])
                ?><!--</div>-->
                <div class="col-xs-8"><?= $form->field($model, 'create_date')->input('search', ['readonly'=>$readonly,'value' => $vlanData['create_date'],'class'=>'form-control datepick']) ?> </div>
                <div class="col-xs-4"><?= $form->field($model, 'backbone_node_id')->input('number', ['readonly' => true, 'value' => $list['node']]) ?></div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <?= $form->field($model, 'description')->textarea(['value'=>$vlanData['description']]) ?>
                </div>
            </div>
            <div class="row">
            </div>
            <? $model->active = $vlanData['active'];?>
            <?= $form->field($model, 'active')->checkbox(['value'=>1, 'uncheckValue'=>0]) /// Здесь какой то глюк ?>
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
        $JS=<<<JS
$('#NewVlan').on('beforeSubmit', function(e) {
        var form = $(this);
        var formData = form.serialize();
        console.log(formData);
        // $.ajax({
        //     url: form.attr("action"),
        //     type: form.attr("method"),
        //     data: formData,
        //     success: function (data) {
        //         ...
        //     },
        //     error: function () {
        //         alert("Something went wrong");
        //     }
        // });
    }).on('submit', function(e){
        e.preventDefault();
    });
JS;

//        $this->registerJS($JS);

    SiteHelper::debug($list);

Pjax::end();