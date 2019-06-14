<?
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-tree"></i> Кедр','encode'=>false];
$this->params['breadcrumbs'][] = ['label' => 'Мастер номеров'];

?>
<div>
    <div class="container-fluid">
        <div class=" row-fluid">
            <table class="table">
                <tr>
                    <td>
                        <div id="wizard_caller_ambi" class="form-inline">
                            <div class="input-group form-group has-feedback form-group-lg">
                                <span class="input-group-addon">3452</span>
                                <input type="text" class="form-control" id="wizard_caller"  placeholder="Введите номер..." maxlength="6">
                                <span  id="wizard_caller_exist" class="form-control-feedback"></span>
                            </div>
                            <div class="input-group" data-toggle="buttons">
                                <label class="btn btn-lg btn-default active">
                                    <input
                                        type="radio"
                                        name="caller_search_type"
                                        checked
                                        id="single"
                                        onchange="
                                            $('#wizard_caller').val('').mask('999999',{completed:function(){ wizard_input($(this).val());}},{placeholder:''}).attr('placeholder','Введите номер...');
                                            $('#wizard_local_user_out').html('');
                                            $('#wizard_caller_exist').html('');
                                            wizard_caller_ambi.removeClass('has-error').removeClass('has-success');

                                            //alert('singlet')
                                        ">Одиночный
                                </label>
                                <label class="btn btn-lg btn-default"
                                       data-toggle="tooltip"
                                       title="Будь внимателен!!!"
                                >
                                    <input
                                        type="radio"
                                        name="caller_search_type"
                                        id="range"

                                        onchange="
                                            $('#wizard_caller').val('').mask('999999-999999',{completed:function(){wizard_input($(this).val());}},{placeholder:''}).attr('placeholder','Введите диапазон...');
                                            $('#wizard_local_user_out').html('');
                                            $('#wizard_caller_exist').html('');
                                            wizard_caller_ambi.removeClass('has-error').removeClass('has-success');
                                            //alert('duplet')
                                        ">Диапазон
                                </label>
                            </div>
                        </div>
                        <!--                        <button class="btn btn-warning" disabled id="confirmation" >Внести изменения</button>-->
                    </td>
                </tr>
                <tr>
                    <td>
                        <div id="wizard_local_user_out"></div>
                    </td>
                </tr>
            </table>
            <div class="row-fluid">
            </div>
        </div>
    </div>
</div>
<?
\common\components\SiteHelper::debug($test);?>