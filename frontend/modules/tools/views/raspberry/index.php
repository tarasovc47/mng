<?php
use yii\helpers\Html;
$this->params['breadcrumbs'][] = ['label' => 'Инструменты'];
$this->params['breadcrumbs'][] = ['label' => 'Конфигурации терминалов '];

?>

<div class="container-fluid ">
    <div class="row-fluid">
        <div class="col-md-8">
            <div id="rpisList">
<!--                <div class="panel panel-default">-->
<!--                    <div class="panel-heading">Список доступных RPi <a id="refresh" class="pull-right"><i class="fa fa-fw fa-refresh"></i></a></div>              -->
<!---->
<!---->
<!--                </div>-->
            </div>
        </div>
        <div class="col-md-4" id="RpiInfo">

        </div>
    </div>
</div>

    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">RPi конфигурация</h4>
                </div>
                <form id="rasp_form">
                <div class="modal-body">

                        <div class="form-group">
                            <label for="mod_user">Пользователь</label>
                            <select class="form-control" id="mod_user" name="user"></select>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-6">
                                <label for="mod_ip">IP: </label>
                                <span id="mod_ip"></span>
                                <input hidden readonly id="input_ip" name="ip" >
                            </div>
                            <div class="col-xs-6">
                                <label for="mod_mac">MAC: </label>
                                <span id="mod_mac"></span>
                                <input hidden readonly id="input_mac" name="mac" >
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="mod_config">Конфигурация</label>
                            <textarea  name="config" rows='10' style='resize: none' class="form-control" id="mod_config"></textarea>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                    <button class="btn btn-primary" type="submit">Сохранить</button>
                </div>
                </form>
            </div>
        </div>
    </div>

<!--<p>1) Добавить МАК в Everyone/list.wtc-->
<!--<p>2) Создать конфиг в Terminals/%<%MAK%>%, продублировать его в БД-->
<?php
//echo "<pre id='debug'>";
//print_r($rpis);
//echo "</pre>";