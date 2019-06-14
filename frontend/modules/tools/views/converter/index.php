<?php
//use yii\widgets\ActiveForm;
//
//use yii\widgets\ActiveField;
use yii\helpers\Html;
$this->params['breadcrumbs'][] = ['label' => 'Инструменты'];
$this->params['breadcrumbs'][] = ['label' => 'Конвертер конфигурации'];


//$form = ActiveForm::begin()?>
<?//= $form->field($model,'zyxel')->label('Начальная конфигурация Zyxel')->textarea(['row'=>5])?>
<?//= $form->field($model,'type')->label('Выберите тип коммутатора')?>
<?//= Html::submitButton('Конвертировать',['class'=>'btn btn-success']) ?>
    <div class="row-fluid">
        <div class="container-fluid">
            <form id="converter" class="" method="post">
                <div class="form-group field-converterform-zyxel row">
                    <label class="control-label" for="zyxel">Начальная конфигурация Zyxel</label>
                    <textarea id="zyxel" required class="form-control" rows="5" placeholder="Вставьте сюда текст конфига zyxel...."></textarea>
                    <div class="help-block"></div>
                </div>
                <label for="type">Тип коммутатора</label>
                <div class="form-inline-group field-converterform-zyxel row">
                    <div class="col-sm-3" >

                        <select class="form-control" id="type">
                            <option value="0">Eltex MES-1124</option>
                            <option value="1">Eltex MES-2124</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <button type="submit" class="btn btn-success">Конвертировать</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <br>
    <div class="row-fluid">
        <div class="container-fluid">
            <div class="form-group field-converterform-zyxel row" data-uk-margin>
                <label class="control-label" for="result">Результат для Eltex</label>
                <textarea class="col-lg-12 form-control" id="result" rows="20"></textarea>
            </div>
        </div>
        <div>


