<?php
use yii\helpers\Html;
$this->params['breadcrumbs'][] = ['label' => 'Инструменты'];
$this->params['breadcrumbs'][] = ['label' => 'Автоконфигурация коммутаторов'];

?>

<div class="row-fluid">
        <div class="container-fluid">
            <div class="panel panel-primary">
            <div class="panel-heading">
            <form id="swconf" class="" method="post">
                <table>
                    <tr>
                        <td><label for="vlan">Vlan</label></td>
                        <td>&nbsp;</td>
                        <td><label for="address">IP адрес</label></td>
                        <td>&nbsp;</td>
                        <td><label for="mac">MAC <span class="label label-warning">XX:XX:XX:XX:XX:XX</span></label></td>
                    </tr>
                    <tr>

                        <td>
                            <select required id="vlan" name="vlan" class="form-control "></select>
                        </td>
                        <td><i class="fa fa-arrow-right fa-fw"></i></td>
                        <td>
                            <select required name="address" id="address" disabled class="form-control"></select>
                        </td>
                        <td><i class="fa fa-arrow-right fa-fw"></i></td>
                        <td>
                            <input
                                required
                                id="mac"
                                name="mac"
                                maxlength="17"
                                minlength="17"
                                disabled
                                class="form-control"
                                pattern="[0-9A-Fa-f]{2}:[0-9A-Fa-f]{2}:[0-9A-Fa-f]{2}:[0-9A-Fa-f]{2}:[0-9A-Fa-f]{2}:[0-9A-Fa-f]{2}">
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <label for="model">Тип коммутатора</label>
                        </td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>

                    <tr>
                        <td>
                            <select required name="model" id="model" class="form-control">
                                <option></option>
                                <option value="MES1024">MES-1024</option>
                                <option value="MES1124">MES-1124</option>
                                <option value="MES2024">MES-2024</option>
                                <option value="MES2124">MES-2124</option>
                                <option value="MES3124">MES-3124</option>
                            </select>
                        </td>
                        <td>&nbsp;</td>
                        <td><button class="btn btn-success" type="submit">OK</button></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td><label for="leases">DHCP пул</label></td>
                    </tr>
                    <tr>
                        <td>
                            <select id="leases" disabled class="form-control">
                                <option>

                                </option>
                            </select>
                        </td>
                        <td>&nbsp;</td>
                        <td>
                            <button disabled id="removeLease" type="button" class="btn btn-danger"><i class="fa fa-trash fa-fw"></i></button>
                        </td>
                    </tr>
                </table>
            </form>
            </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row-fluid">
        <div class="container-fluid">
            <div class="form-group field-converterform-zyxel row" data-uk-margin>
                <label class="control-label" for="result">Результат для Eltex</label>
                <textarea class="col-lg-12 form-control" id="result" rows="20"><?
                    //A8:F9:4B:2C:2B:80?></textarea>
            </div>
        </div>
    <div>
