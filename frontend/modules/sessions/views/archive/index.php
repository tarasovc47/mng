<?php
//use common\components\SiteHelper;
//use yii\web\Session;
use yii\widgets\Pjax;
use common\components\SiteHelper;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
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
$this->params['breadcrumbs'][] = ['label' => 'Архив сессий'];
if($permissions['blacklist']>0) {
    $this->params['breadcrumbs'][] = [
        'label' => 'Черный список',
        'url' => '/sessions/blacklist',
        'template' => "<li>{link}</li>\n", // template for this link only
    ];
}
Pjax::begin([
    'id' => 'archive_table',
    'timeout' => false,
    'enablePushState' => false,
    'clientOptions' => ['method' => 'POST']
]);
?>
<style>
    #w0-filters {
        display: none;
    }
</style>
<div class="row">
    <div class="col-xs-12 col-lg-2 col-sm-4"><?= $this->render('_search', ['model' => $searchModel]) ?></div>
    <div class="col-xs-12 col-lg-10 col-sm-8"><?
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
                        $tmp = "<span>".$data['login']."</span><br><small class='text-muted'>".$data['mac_addr']."</small>";
//                return SiteHelper::debug($data);
                        return $tmp;
                    }
                ],
                [
                    'attribute'=>'downstream_octets',
                    'header'=>'<i class="fa fa-download"></i> Скачано<br> <i class="fa fa-upload"></i> Отдано',

                    'content'=>function($data){
                        $tmp = "<span><i class=\"fa fa-download text-success\"></i> ".SiteHelper::FBytes($data['downstream_octets'])."</span><br><span><i class=\"fa fa-upload text-warning\"></i> ".SiteHelper::FBytes($data['upstream_octets'])."</span>";
                        return $tmp;
                    }
                ],

                [
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
                ],
                [
                    'attribute'=>'started_at',
                    'header'=>'Соединено<br>  Завершено',
                    'content'=>function($data){
                        $tmp = "<span>".$data['started_at']."</span><br><span>".$data['stopped_at']."</span>";
                        return $tmp;
                    }
                ],
                [
                    'attribute'=>'svcs_log',
                    'header'=>'Сервисы',
                    'content'=>function($data){
                        $svc_data = [];
                        $json = $data['svcs_log'];
                        $json = str_replace('{','[',$json);
                        $json = str_replace('}',']',$json);
                        $json = str_replace('\"','"',$json);
                        $json = str_replace('"(','[',$json);
                        $json = str_replace(')"','"]',$json);
                        $json = str_replace(')"','"]',$json);
                        $json = str_replace(',','","',$json);
                        $json = str_replace('""','"',$json);
                        $json = str_replace(']","[','],[',$json);
                        $svcs = json_decode($json);
                        $i = 0;
                        if(!empty($svcs)) {
                            foreach ($svcs as $svc) {
                                $i++;
                                $svc_data[$i] = ' <span>' . explode(".", $svc[0])[0] . '</span> ';
                                $svc_data[$i] .= ' <span class="text-primary">' . $svc[1] . '</span> ';
                                switch ($svc[2]) {
                                    case "S":
                                        $svc_data[$i] .= '<b class="text-success ">' . $svc[2] . '</b>';
                                        break;

                                    case "D":
                                        $svc_data[$i] .= '<b class="text-danger ">' . $svc[2] . '</b>';
                                        break;

                                    case "A":
                                        $svc_data[$i] .= '<b class="text-info ">' . $svc[2] . '</b>';
                                        break;
                                }
//                    $svc_data[] .= $svc[0].$svc[1].' '.$svc[2];
                            }
                            sort($svc_data);
                        }
                        return implode("<br>",$svc_data);
                    }
                ],

                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view}'
                ],
            ],
        ]);
        ?>
    </div>
</div><?
Pjax::end();