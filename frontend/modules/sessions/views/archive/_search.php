<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

   $form = ActiveForm::begin([
    'id' => 'archive_form',
    'method' => 'post',
    'action' => ['index']
//    'enableClientValidation' => false,
//    'enableAjaxValidation' => true,
//    'validationUrl' => '/sessions/archive/validate',
//    'options' => ['data-pjax' => true]
    ]);
    ?>
<div class="radiusarch-search">
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
        'maxlength'=>17,
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
//                    'disabled'=>true
//                               'placeholder' => 'XX:XX:XX:XX:XX:XX',
    ])
    ->label(false) ?>
    <br>
<?= $form
    ->field($model, 'begin')
    ->textInput([
        'placeholder'=>$model->getAttributeLabel('begin'),
    ])
    ->label(false) ?>
<?= $form
    ->field($model, 'end')
    ->textInput([
        'placeholder'=>$model->getAttributeLabel('end'),
    ])
    ->label(false) ?>
    <button type="submit" class="btn btn-success btn-xs btn-block">Поиск</button>
<? ActiveForm::end(); ?></div>