<?php
use yii\helpers\Html;
use common\components\SiteHelper;
use yii\web\Session;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;
use yii\widgets\Pjax;
use yii\grid\GridView;
$session = Yii::$app->session;


$this->params['breadcrumbs'][] = [
    'label' => 'Подключения',
    'url' => '/sessions/',
    'template' => "<li>{link}</li>\n", // template for this link only
];
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
$this->params['breadcrumbs'][] = ['label' => 'Черный список'];
$form = ActiveForm::begin([
    'id' => 'blacklist_form',
    'method' => 'post',
    'enableClientValidation' => false,
    'enableAjaxValidation' => true,
    'validationUrl' => '/sessions/blacklist/validate',
    'options' => ['data-pjax' => true]
]);

?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="form-group">
            <div class="col-lg-2 ">
                <?= $form
                    ->field($searchModel, 'login')
                    ->textInput([
                        'autofocus'=>true,
                        'placeholder'=>$searchModel->getAttributeLabel('login'),
                    ])->label(false) ?>
            </div>
            <div class="col-lg-2 col-xs-6">

                <?= $form
                    ->field($searchModel, 'mac_address')
                    ->textInput([
                        'placeholder'=>$searchModel->getAttributeLabel('mac_address'),
                    ])->label(false) ?>
            </div>
            <input hidden name="AccountingBlacklist[save]" value="1">
            <button type="submit" class="btn btn-flat btn-default">+</button>
        </div>
<?
ActiveForm::end();?>
    </div>
</div>
<?

Pjax::begin([
    'id' => 'blacklist_table',
    'timeout' => false,
    'enablePushState' => false,
    'clientOptions' => ['method' => 'POST']
]);
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'tableOptions' => [
        'class' => 'table table-hover table-condensed table-striped '
    ],
//            'showOnEmpty'=>true, // показывать всегда
//            'emptyCell'=>'-',
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute'=>'login',
            'header'=>'Логин',
            'content'=>function($data){
                $tmp = "<span>".$data['login']."</span><br><small class='text-muted'>".$data['mac_address']."</small>";
//                return SiteHelper::debug($data);
                return $tmp;
            }
        ],
        /*[
            'attribute'=>'mac_address',
            'header'=>'Логин',
            'content'=>function($data){
                $tmp = "<span>".$data['login']."</span><br><small class='text-muted'>".$data['mac_address']."</small>";
//                return SiteHelper::debug($data);
                return $tmp;
            }
        ],*/

        /*[
            'attribute'=>'ipv4_addr',
            'header'=>'Адреса',
//            'contentOptions' =>function ($model, $key, $index, $column){
//                return ['class' => 'name'];
//            },
            'content'=>function($data){
                $tmp = "<span>IP: <b>".$data['ipv4_addr']."</b></span><br><small class='text-muted'>";
                if($data['ipv6_prefix']!=''){
                    $tmp .="IPv6 prefix: <b>".$data['ipv6_prefix']."</b></small>";
                }
                return $tmp;
            }
        ],*/
        [
            'attribute'=>'banned_at',
            'header'=>'Добавлено в список',
            'content'=>function($data){
                $tmp = "<span>".date("H:i:s Y:m:d",$data['banned_at'])."</span>";
                return $tmp;
            }
        ],


        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view} {delete}'
        ],
    ],
]);
Pjax::end();
?>