<?
use yii\widgets\ActiveForm;
use common\components\SiteHelper;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
$this->params['breadcrumbs'][] = [
    'label' => 'Подключения',
    'url' => '/sessions/',
    'template' => "<li>{link}</li>\n", // template for this link only
];
$this->params['breadcrumbs'][] = ['label' => 'Активные'];
if($permissions['history']>0) {
    $this->params['breadcrumbs'][] = [
        'label' => 'Архив сессий',
        'url' => '/sessions/archive/',
        'template' => "<li>{link}</li>\n", // template for this link only
    ];
}
if($permissions['blacklist']>0) {
    $this->params['breadcrumbs'][] = [
        'label' => 'Черный список',
        'url' => '/sessions/blacklist',
        'template' => "<li>{link}</li>\n", // template for this link only
    ];
}
//print_r($cas_user);die();
Pjax::begin();
?>
<div class="row">
    <div class="col-xs-12 col-lg-3 col-md-4 col-sm-4"><?
    $form = ActiveForm::begin([
        'id' => 'accounting_form',
                'method' => 'post',
                'enableClientValidation' => false,
                'enableAjaxValidation' => true,
                'validationUrl' => '/sessions/validate',
                'options' => ['data-pjax' => true]
    ]);?>

    <?= $form
        ->field($model, 'login')
        ->textInput([
            'autofocus'=>true,
            'placeholder'=>$model->getAttributeLabel('login'),
        ])->label(false) ?>
    <?= $form
        ->field($model, 'macaddr')
        ->textInput([
            'placeholder'=>$model->getAttributeLabel('macaddr'),
            'title'=>'XX:XX:XX:XX:XX:XX или XXXX.XXXX.XXXX',
            'data-toggle'=>'tooltip',
//                               'placeholder' => 'XX:XX:XX:XX:XX:XX',
            'maxlength'=>17
        ])
        ->label(false) ?>
    <?= $form
        ->field($model, 'ipv4')
        ->textInput([
            'placeholder'=>$model->getAttributeLabel('ipv4'),
//                               'placeholder' => 'XX:XX:XX:XX:XX:XX',
        ])
        ->label(false) ?>
    <?= $form
        ->field($model, 'ipv6')
        ->textInput([
            'placeholder'=>$model->getAttributeLabel('ipv6'),
        ])
        ->label(false) ?>
    <?= Html::submitButton('Поиск', ['class' => 'btn btn-info btn-xs btn-block']); ?>
    <? ActiveForm::end();
    ?></div>
    <div class="col-xs-12 col-lg-9 col-md-8 col-sm-8">
        <hr class="visible-xs">
        <?=$accountingCard?>
    </div>
</div><?php


Pjax::end();
Modal::begin([
    'header' => '<h4>Завершить сессию?</h4>',
    'footer' => Html::button('Подтвердить', ["class" => "btn btn-success","id"=>"confirm_btn"]) .
        Html::button('Отмена', ["class" => "btn btn-default cancel", "data-dismiss" => "modal"]),
    'id' => 'confirm',
    'closeButton' => false,
]);
?>
    <div class="confirm-content"></div>
<?php Modal::end(); ?>

<?php
Modal::begin([
    'footer' => Html::button('Закрыть', ["class" => "btn btn-default cancel", "data-dismiss" => "modal"]),
    'id' => 'notice',
    'closeButton' => false,
]);
?>

    <div class="notice-content"></div>
<?php Modal::end();
