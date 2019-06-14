<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ContactFaces */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="contact-faces-form">

    <?php $form = ActiveForm::begin(['enableClientValidation' => false, 'enableAjaxValidation' => false]); ?>

    <?= $form->field($model, 'name')->textInput() ?>

    <div class="contact-faces__add-phone">
        <?php  
            if (empty($model->phones)){
                echo '<div class="row">'
                        .'<div class="col-md-4">'
                        .$form->field($model, 'phones[]', ['template'=>'{label}<div class="input-group"><span class="input-group-addon">+7</span>{input}<span class="input-group-btn"><button class="btn btn-default contact-faces__remove-phone-button" type="button"><i class="fa fa-times" aria-hidden="true"></i></button></span></div>{error}', 'errorOptions' => ['class' => 'help-block' ,'encode' => false]])->textInput(['id' => false, 'class' => 'contactfaces-phones form-control', 'data' => ['phone-id' => '']])
                        .'</div>'
                        .'<div class="col-md-8">'
                        .$form->field($model, 'phones_comments[0]')->textInput(['id' => false, 'class' => 'contactfaces-phones_comments form-control'])
                        .'</div>'
                        .'</div>';
            } else {
                $i = 1;
                foreach ($model->phones as $key => $phone){
                    if ($i == 1) {
                        echo '<div class="row">'
                                .'<div class="col-md-4">'
                                .$form->field($model, 'phones['.$key.']', ['template'=>'{label}<div class="input-group"><span class="input-group-addon">+7</span>{input}<span class="input-group-btn"><button class="btn btn-default contact-faces__remove-phone-button" type="button"><i class="fa fa-times" aria-hidden="true"></i></button></span></div>{error}', 'errorOptions' => ['class' => 'help-block' ,'encode' => false]])->textInput(['id' => false, 'class' => 'contactfaces-phones form-control', 'value' => $phone, 'data' => ['phone-id' => $key]])
                                .'</div>'
                                .'<div class="col-md-8">'
                                .$form->field($model, 'phones_comments['.$key.']')->textInput(['id' => false, 'class' => 'contactfaces-phones_comments form-control'])
                                .'</div>'
                                .'</div>';
                    } else {
                        echo '<div class="row">'
                                .'<div class="col-md-4">'
                                .$form->field($model, 'phones['.$key.']', ['template'=>'<div class="input-group"><span class="input-group-addon">+7</span>{input}<span class="input-group-btn"><button class="btn btn-default contact-faces__remove-phone-button" type="button"><i class="fa fa-times" aria-hidden="true"></i></button></span></div>{error}', 'errorOptions' => ['class' => 'help-block' ,'encode' => false]])->textInput(['id' => false, 'class' => 'contactfaces-phones form-control', 'value' => $phone, 'data' => ['phone-id' => $key]])->label(false)
                                .'</div>'
                                .'<div class="col-md-8">'
                                .$form->field($model, 'phones_comments['.$key.']')->textInput(['id' => false, 'class' => 'contactfaces-phones_comments form-control'])->label(false)
                                .'</div>'
                                .'</div>'; 
                    }
                    $i++;
                }
            }
        ?>
        <div class="form-group">
            <?= Html::button('<i class="fa fa-plus" aria-hidden="true"></i> Добавить ещё номер телефона', ['class' => 'btn btn-xs btn-link contact-faces__add-another-phone']) ?>
        </div>
    </div>

    <div class="contact-faces__add-email">
        <?php  
            if (empty($model->emails)){
                echo '<div class="row">'.
                        '<div class="col-md-4">'
                        .$form->field($model, 'emails[0]', ['template'=>'{label}<div class="input-group">{input}<span class="input-group-btn"><button class="btn btn-default contact-faces__remove-email-button" type="button"><i class="fa fa-times" aria-hidden="true"></i></button></span></div>{error}'])->textInput(['id' => false, 'class' => 'contactfaces-emails form-control', 'data' => ['email-id' => '']])
                        .'</div>'
                        .'<div class="col-md-8">'
                        .$form->field($model, 'emails_comments[0]')->textInput(['id' => false, 'class' => 'contactfaces-emails_comments form-control'])
                        .'</div>'
                        .'</div>';
            } else {
                $i = 1;
                foreach ($model->emails as $key => $email){
                    if ($i == 1) {
                        echo '<div class="row">'.
                                '<div class="col-md-4">'
                                .$form->field($model, 'emails['.$key.']', ['template'=>'{label}<div class="input-group">{input}<span class="input-group-btn"><button class="btn btn-default contact-faces__remove-email-button" type="button"><i class="fa fa-times" aria-hidden="true"></i></button></span></div>{error}'])->textInput(['id' => false, 'class' => 'contactfaces-emails form-control', 'value' => $email, 'data' => ['email-id' => $key]])
                                .'</div>'
                                .'<div class="col-md-8">'
                                .$form->field($model, 'emails_comments['.$key.']')->textInput(['id' => false, 'class' => 'contactfaces-emails_comments form-control'])
                                .'</div>'
                                .'</div>';
                    } else {
                        echo '<div class="row">'.
                                '<div class="col-md-4">'
                                .$form->field($model, 'emails['.$key.']', ['template'=>'{label}<div class="input-group">{input}<span class="input-group-btn"><button class="btn btn-default contact-faces__remove-email-button" type="button"><i class="fa fa-times" aria-hidden="true"></i></button></span></div>{error}'])->textInput(['id' => false, 'class' => 'contactfaces-emails form-control', 'value' => $email, 'data' => ['email-id' => $key]])->label(false)
                                .'</div>'
                                .'<div class="col-md-8">'
                                .$form->field($model, 'emails_comments['.$key.']')->textInput(['id' => false, 'class' => 'contactfaces-emails_comments form-control'])->label(false)
                                .'</div>'
                                .'</div>';
                    }
                    $i++;
                }
            }
        ?>
        <div class="form-group">
            <?= Html::button('<i class="fa fa-plus" aria-hidden="true"></i> Добавить ещё электронную почту', ['class' => 'btn btn-xs btn-link contact-faces__add-another-email']) ?>
        </div>
    </div>

    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
