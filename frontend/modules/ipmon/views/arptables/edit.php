<?php
use yii\widgets\Breadcrumbs;
use common\components\SiteHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\widgets\Pjax;

Pjax::begin();
$h1 = "Опорная сеть: ARP";
use common\widgets\AttributesTree;
if(isset($id)){
    $breadcrumbTitle = "Редактирование ".long2ip($id)." (".$router['description'].")";
}else{
    $breadcrumbTitle = 'Новый роутер';
}
echo Breadcrumbs::widget([
    'itemTemplate' => "<li>{link}</li>\n", // template for all links
    'links' => [
        [
            'label' => 'ARP таблицы',
            'url' => '/ipmon/arptables',
            'template' => "<li>{link}</li>\n", // template for this link only
        ],

        $breadcrumbTitle,

    ],
]);

$readonly = false;
if(!empty($router['user'])){
    $readonly = true;
}
?><a type="button" class="close" href="/ipmon/arptables"><span aria-hidden="true">&times;</span></a><br><?

/*
 * 'id' => 'ID',
            'ip' => 'IP адрес',
            'description' => 'Описание',
            'user' => 'Пользователь read',
            'apipass' => 'Пароль read',
            'rwuser' => 'Пользователь write',
            'apiwrpass' => "Пароль write",
            'visible' => 'Видимость',
 */
?> <div class="container-fluid"><?
$form = ActiveForm::begin([
    'id' => 'editRouter',
    'method' => 'post',
    'enableClientValidation' => false,
    'enableAjaxValidation' => true,
    'action'=>'/ipmon/arptables',
    'validationUrl' => '/ipmon/arptables/validate',
    'options' => ['data-pjax' => true]
]);
?><div class="row">
        <div class="col-md-4 col-xs-12"><?
            echo $form->field($model, 'ip')->input('text', ['value' => $router['ip']]);
        ?></div><?
        ?><div class="col-md-8 col-xs-12"><?
            echo $form->field($model, 'description')->input('text', ['value' => $router['description']]);
        ?></div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="row">
                <?
                ?><div class="col-md-6 col-xs-12"><?
                    echo $form->field($model, 'user')->input('text', ['readonly' => $readonly, 'value' => $router['user']]);
                    ?></div><?
                ?><div class="col-md-6 col-xs-12"><?
                    echo $form->field($model, 'apipass')->input('text', ['readonly' => $readonly, 'value' => $router['apipass']]);
                    ?></div>
            </div>
            <div class="row"><?
                ?><div class="col-md-6 col-xs-12"><?
                    echo $form->field($model, 'rwuser')->input('text', [ 'value' => $router['rwuser']]);
                    ?></div><?
                ?><div class="col-md-6 col-xs-12"><?
                    echo $form->field($model, 'apiwrpass')->input('text', ['value' => $router['apiwrpass']]);
                    ?></div><?
                ?></div>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12"><?
                    echo $form->field($model, 'text')->textarea(['rows' => 4,'value' => $router['text']]);
                    ?></div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-xs-12"><?
            $model->visible = $router['visible'];?>
            <?= $form->field($model, 'visible')->checkbox(['value'=>1, 'uncheckValue'=>0]);
        ?></div>
    </div>
    <div class="row">
        <b>Настройка видимости сетей</b>
        <div class="panel panel-default">
            <div class="panel-heading"><div class="col-lg-6"><?
                $i=0;

//                    SiteHelper::debug($cas['cas_id']);
//                foreach ($data as $datum) {
//                    $i++;
//                    if ($datum != 'timeout') {
//                        echo "<li>".$datum['network'];
//                    }
//                }
            ?></div>
                <div class="col-lg-6">
                </div>
            </div>
        </div>
    </div>
    <div class="row"><?
        ?><a href="/ipmon/arptables" class="btn btn-default">Закрыть</a>
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']);
    ?></div>
</div><?
ActiveForm::end();
?><div class="row">
<!--    <div class="col-xs-4">--><?//SiteHelper::debug($data);?><!--</div>-->
<!--    <div class="col-xs-4">--><?//SiteHelper::debug($cas);?><!--</div>-->
<!--    <div class="col-xs-4">--><?//SiteHelper::debug($access);?><!--</div>-->
</div><?

//if(isset($post))SiteHelper::debug($post);
//c$vlans[$j]['comment'], 'utf-8', mb_detect_encoding($vlans[$j]['comment'])),  //кодировка
Pjax::end();