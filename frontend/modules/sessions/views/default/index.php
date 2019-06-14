<?php
use yii\helpers\Html;
use common\components\SiteHelper;
use yii\web\Session;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;
use yii\widgets\Pjax;
$session = Yii::$app->session;


$this->params['breadcrumbs'][] = ['label' => 'Подключения'];
$this->params['breadcrumbs'][] = [
    'label' => 'Активные',
    'url' => '/sessions/accounting/',
    'template' => "<li>{link}</li>\n", // template for this link only
];
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
echo SiteHelper::dataFromPHPtoJS('userinfo', ["login"=>$user->login,"id"=>$user->id,"sid"=>$session['sid']]);
echo SiteHelper::dataFromPHPtoJS('naslist', $nas);

$nas_list = [];
$status_list = [
    'ok'=>'OK',
    'bp'=>'Fail: pasword',
    'bm'=>'Fail: MAC',
    'nu'=>'Fail: user',
];

foreach ($nas as $asr){
    $nas_list[$asr['id']]=$asr['ipaddress'];
}
$params = [

];
Pjax::begin();
?>
<div class="row">
    <!-- Tab panes -->
    <?php
    $form = ActiveForm::begin([
        'id' => 'subscriber_auth_filter',
    ]); ?>
    <div  class="form-inline col-xs-12" >
        <?= $form
            ->field($model, 'login')
            ->textInput([
                'autofocus'=>true,
                'placeholder'=>$model->getAttributeLabel('login'),
                'class'=>'form-control subscriber_auth_filter',
            ])->label(false) ?>
        <?= $form
            ->field($model, 'macaddr')
            ->textInput([
                'placeholder'=>$model->getAttributeLabel('macaddr'),
                'class'=>'form-control subscriber_auth_filter',
                'title'=>'XXXXXXXXXXXX',
                'data-toggle'=>'tooltip',
//                               'placeholder' => 'XX:XX:XX:XX:XX:XX',
                'maxlength'=>17
            ])
            ->label(false) ?>
        <? /* ?> <div ><?= $form
                           ->field($model, 'ipv4')
                           ->textInput([
                               'autofocus'=>true,
                               'placeholder'=>$model->getAttributeLabel('ipv4'),
                               'class'=>'form-control subscriber_auth_filter',
                           ])
                           ->label(false) ?></div>
                   <div ><?= $form
                           ->field($model, 'ipv6')
                           ->textInput([
                               'autofocus'=>true,
                               'placeholder'=>$model->getAttributeLabel('ipv6'),
                               'class'=>'form-control subscriber_auth_filter',
                           ])
                           ->label(false) ?></div>
                   <? */ ?><?=
        $form
            ->field($model, 'status')
            ->label(false)
            ->dropDownList($status_list,[
                'prompt' => 'Любой статус',
                'class'=>'form-control subscriber_auth_filter',
            ]);
        ?>
        <?=
        $form
            ->field($model, 'nas')
            ->label(false)
            ->dropDownList($nas_list,[
                'prompt' => 'Любой NAS',
                'class'=>'form-control subscriber_auth_filter',
            ]);
        ?>&nbsp;<button class="btn btn-default btn-xs" id="subscriber_auth_filter_reset">Сброс</button>
    </div>
    <? ActiveForm::end();

    ?>

</div>
<hr>
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-8">
                        <div class="form-group-sm pull-left form-inline">
                            <select style="width: 60px!important;" class="form-control hidden-xs" id="max_subscriber_auth_list_length">
                                <option>5</option>
                                <option>10</option>
                                <option selected>20</option>
                                <option>50</option>
                            </select>
                            <label for="max_subscriber_auth_list_length">Текущие попытки аутентификации</label>
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div class="form-group pull-right">
                            <span id="status" class="label label-default hidden-xs">Connecting...</span>&nbsp;
                            <button id="pause" class="btn btn-xs btn-warning">
                                <i id="pauseIcon" class="fa fa-pause"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-condensed table-hover">
                <thead>
                <tr>
                    <th>&nbsp;</th>
                    <th>Логин</th>
                    <th>Дата</th>
                    <th>circuit id</th>
                    <th>Статус</th>
                    <th class="visible-lg">NAS</th>
                </tr>
                </thead>
                <tbody  id="subscriber_auth_list">
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-6">
        <div class="row">
            <div class="col-xs-12 hidden" id="startTable">
                <div class="panel panel-success">
                    <div  class="panel-heading">
                        Последние 12 стартовавших сессии
                    </div>
                    <table class="table table-hover table-condensed ">
                        <thead>
                        <tr class="success">
                            <th>Логин</th>
                            <th>Дата</th>
                            <th>MAC адрес</th>
                        </tr>
                        </thead>
                        <tbody   id="accounting_start" >

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 hidden"  id="stopTable">
                <div class="panel panel-warning">
                    <div  class="panel-heading">
                        Последние 12 завершенных сессий
                    </div>
                    <table  class="table table-hover">
                        <thead>
                        <tr class="warning">
                            <th>Логин</th>
                            <th>Дата</th>
                            <th>MAC адрес</th>
                        </tr>
                        </thead>
                        <tbody   id="accounting_stop" >

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
    <!-- Modal -->
    <div class="modal fade" id="accountingModal" tabindex="-1" role="dialog" aria-labelledby="accountingModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content modal-lg">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h3 id="modalTitle"></h3>
                </div>
                <div class="modal-body" id="AccountingBody">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>


<!--    --><?// SiteHelper::debug($nas); ?>
<?php
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
?><div class="notice-content"></div><?

