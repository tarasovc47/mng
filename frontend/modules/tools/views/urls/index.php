<?php
use yii\helpers\Html;
$this->params['breadcrumbs'][] = ['label' => 'Инструменты'];
$this->params['breadcrumbs'][] = ['label' => 'Генерация ссылок'];
//Представление времменное
?>
<div class="row-fluid">
    <div class="container-fluid">
        <div class="col-lg-4 col-sm-8 col-md-6">
            <div class="panel panel-success">
                <div class="panel-heading">
                    Генерация ссылок оплаты
                </div>
                <div class="panel-body">
                    <form id="link_form">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label>Лицевой счет
                                    <input required name="account" class="form-control">

                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label>Сумма
                                    <input type="number" step="0.01" min="0.00" required name="amount" class="form-control" value="0.00">
                                </label>
                            </div>
                        </div>
                        <button class="btn btn-success btn-block">Создать</button>
                    </form>
                    <hr>

                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div id="link"></div>
        </div>
    </div>
</div>
