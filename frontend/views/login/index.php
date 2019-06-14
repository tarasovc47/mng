<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap4;
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Sign In';

$fieldOptions1 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-user form-control-feedback'></span>"
];

$fieldOptions2 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];
/*?>

<div class="login-box">
    <div class="login-logo">
        <a href="#"><b>mng</b></a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">Необходимо авторизоваться</p>
<? */?>
    <div class="col-md-3 col-lg-4">&nbsp;</div>
    <div class="col-md-6 col-lg-4">

        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            'method'=>'post',
            'options'=>[
                    'class'=>'form-horizontal'
                ]
        ]); ?>
    <span class="heading">MNG<br>авторизация</span>
        <?= $form
            ->field($model, 'username', $fieldOptions1)
            ->label(false)
            ->textInput(['placeholder' => $model->getAttributeLabel('username'),'autofocus' => true]) ?>

        <?= $form
            ->field($model, 'password', $fieldOptions2)
            ->label(false)
            ->passwordInput(['placeholder' => $model->getAttributeLabel('password')]) ?>

        <div class="form-group">
<!--            <div class="main-checkbox">-->
<!--                <input type="checkbox" value="none" id="checkbox1" name="LoginForm[rememberMe]"/>-->
<!--                <label for="checkbox1"></label>-->
<!--            </div>-->
<!--            <span class="text">Запомнить</span>-->
            <button type="submit" class="btn btn-default">ВХОД</button>
        </div>
        <? /*?><div class="main-checkbox">
                <?= $form->field($model, 'rememberMe')->checkbox(['class'=>'']) ?>
            <!-- /.col -->
                <?= Html::submitButton('Вход', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            <!-- /.col -->
        </div> <?*/?>
        <?php ActiveForm::end(); ?>
    </div>
        <?/*?>

    </div>
    <!-- /.login-box-body -->
</div><!-- /.login-box -->
*/?>