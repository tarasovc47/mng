<?php
use yii\widgets\ActiveForm;
$this->params['breadcrumbs'][] = ['label' => 'Инструменты'];
$this->params['breadcrumbs'][] = ['label' => 'Конфигурации терминалов '];
$this->params['breadcrumbs'][] = ['label' => $model->ip];
?>

<!-- Modal -->
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">RPi конфигурация</h4>
            </div>
                <div class="modal-body">

                    <?php $form = ActiveForm::begin([

                        'method' => 'post',
                        'enableClientValidation' => false,
                        'enableAjaxValidation' => true,
//    'action'=>'/admin/streams',
//    'validationUrl' => '/tools/rpi/validate/',
                        'options' => ['data-pjax' => true]
                    ]); ?>
                    <div class="row">
                        <div class="col-xs-5">
                            <?= $form->field($model, 'ip')->label(false)->textInput([
                                'placeholder' => $model->getAttributeLabel('ip'),
                                'readonly'=>true
                            ]) ?>
                        </div>
                        <div class="col-xs-7">
                            <?= $form->field($model, 'mac')->label(false)->textInput([
                                'placeholder' => $model->getAttributeLabel('mac'),
                                'readonly'=>true
                            ]);?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <?= $form->field($model, 'config')->label(false)->textarea([
                                'placeholder' => $model->getAttributeLabel('config'),
                                'rows'=>10
                            ]) ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a type="button" class="btn btn-default" href="/tools/rpi/">Закрыть</a>
                        <button class="btn btn-primary" type="submit">Сохранить</button>
                    </div>
                    <?php
                    ActiveForm::end();
                    ?>

        </div>
    </div>

