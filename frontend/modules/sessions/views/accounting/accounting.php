<?php
use common\components\SiteHelper;

$this->params['breadcrumbs'][] = [
    'label' => 'Сессии',
    'url' => '/sessions'
];
$this->params['breadcrumbs'][] = ['label' => 'Карточка сессии'];
$h=0;
$nums = 0;
?><div class="">
    <ul class="nav nav-tabs" role="tablist"><?
  foreach ($accounting as $radius => $accounts){
      if($accounts['accounting_nums']>0){
          $nums++;
          $active = '';
          if($h==0){
              $active = 'active';
          }
          $h++;
          foreach ($accounts['data'] as $account){
              ?><li role="presentation" class="<?=$active?>">
                    <a href="#tab<?=$h?>" aria-controls="tab<?=$h?>" role="tab" data-toggle="tab"><b><?=$account['session_name']?></b></a>
              </li><?
          }
      }
  }
  if($nums==0){
      ?><div class="alert alert-warning">
          Нет активных сессий
      </div><?
  }
  ?></ul>
    <div class="tab-content">
        <?
        $h=0;
        foreach($accounting as $radius => $account){
            if($account['accounting_nums']>0){
                $active = '';
                if($h==0){
                    $active = 'active';
                }
//                SiteHelper::debug($account);

                $h++;
                foreach ($account['data'] as $data){
                    ?><div role="tabpanel" class="tab-pane <?=$active?>" id="tab<?=$h?>">
                    <ul class='list-group'>
                    <li class="list-group-item ">
                    <div class="row">
                        <div class="col-lg-4 col-sm-4">
                            <p class="list-group-item-text text-muted"><i>Логин</i></p>
                            <h4 class="list-group-item-heading"><?=$data['login']?></h4>
                            <p class="list-group-item-text text-muted">RADIUS ID: <?=$data['session_name']?></p>
                        </div>
                        <div class="col-lg-4 col-sm-4">
                            <p class="list-group-item-text text-muted"><i>Статус</i></p>
                            <h4 class="list-group-item-heading text-success">Активна</h4>
                        </div>
                        <? if($permissions['kill_sess']>1){
                            ?><div class="col-lg-4 col-sm-4">
                            <p class="list-group-item-text text-muted"><i>Disconnect</i></p>
                            <a href="#" onclick="disconnect('<?=$data['session_name']?>','<?=$data['login']?>');">
                                <span class="fa-stack"><i class="fa fa-fw fa-exchange fa-stack-1x"></i><i class="fa fa-ban fa-stack-2x text-danger"></i></span>
                            </a>
                            </div><?
                        }
                        ?></div>
                    </li><?
                    if (($data['ipv4_addr'] != '') || ($data['ipv6_prefix'] != '')) {
                        ?><li class="list-group-item ">
                        <div class="row"><?
                            if ($data['ipv4_addr'] != '') {
                                ?><div class="col-lg-4 col-sm-4">
                                <p class="list-group-item-text text-muted"><i>IP-адрес</i></p>
                                <h4 class="list-group-item-heading"><?=$data['ipv4_addr']?></h4>
                                </div>
                                <hr class="visible-xs"><?
                            }
                            if ($data['ipv6_prefix'] != '') {
                                ?><div class="col-lg-4 col-sm-4">
                                <p class="list-group-item-text text-muted"><i>IPv6 префикс</i></p>
                                <h4 class="list-group-item-heading"><?=$data['ipv6_prefix']?></h4>
                                </div><?
                            }
                            ?><div class="col-lg-4 col-md-4">
                                <p class="list-group-item-text text-muted"><i>MAC-адрес</i></p>
                                <h4 class="list-group-item-heading"><?=$data['calling_station']?></h4>
                            </div>
                        </div>
                        </li><?
                    }
                    $uptime_tmp = SiteHelper::Seconds2Times(time() - strtotime($data['started_at']));
                    $uptime = $uptime_tmp[0] . "c";
                    if (isset($uptime_tmp[1])) {
                        $uptime = $uptime_tmp[1] . "м " . $uptime;
                    }
                    if (isset($uptime_tmp[2])) {
                        $uptime = $uptime_tmp[2] . "ч " . $uptime;
                    }
                    if (isset($uptime_tmp[3])) {
                        $uptime = $uptime_tmp[3] . "д " . $uptime;
                    }
                if (1) {
                    ?><li class="list-group-item "><div class="row">
                            <div class="col-sm-4">
                                <p class="list-group-item-text text-muted"><i>Время начала сессии</i></p>
                                <h4 class="list-group-item-heading"><?=explode(".", $data['started_at'])[0]?></h4>
                            </div>
                            <hr class="visible-xs">
                            <div class="col-sm-4">
                                <p class="list-group-item-text text-primary"><i>Время последнего alive пакета</i></p>
                                <h4 class="list-group-item-heading"><?=explode(".", $data['updated_at'])[0]?></h4>
                            </div>
                            <hr class="visible-xs">
                            <div class="col-sm-4">
                                <p class="list-group-item-text text-success"><i>Время жизни сессии</i></p>
                                <h4 class="list-group-item-heading"><?=$uptime?></h4>
                            </div>
                        </div>
                    </li><?
                    if ($data['downstream_octets'] == null) {
                        $data['downstream_octets'] = 0;
                    }
                    if ($data['upstream_octets'] == null) {
                        $data['upstream_octets'] = 0;
                    }

                    ///Инфомация о загруженных/скачанных
                    ///
                    ?><li class="list-group-item "><div class="row">
                            <div class="col-xs-4">
                                <p class="list-group-item-text text-muted"><i>Отправлено</i></p>
                                <h4 class="list-group-item-heading"><?=SiteHelper::FBytes($data['upstream_octets'])?><i class="fa fa-arrow-up fa-fw"></i></h4>
                            </div>
                            <div class="col-xs-4">
                                <p class="list-group-item-text text-muted"><i>Принято</i></p>
                                <h4 class="list-group-item-heading"><?=SiteHelper::FBytes($data['downstream_octets'])?><i class="fa fa-arrow-down fa-fw"></i></h4>
                            </div>
                        </div>
                    </li><?
                    ///Инфомация о Radius (скрыт на мобильниках)
                    ?><li class="list-group-item hidden-xs">
                        <div class="row">
                            <div class="col-xs-4">
                                <p class="list-group-item-text text-muted"><i>NAS сервер</i></p>
                                <h4 class="list-group-item-heading"><?=$data['nas_ipaddr']?></h4>
                            </div><?

                            //                        $json['html'] .= '<div class="col-xs-4">';
                            //                        $json['html'] .= '<h4 class="list-group-item-heading">' . $sessions['portid'] . '</h4>';
                            //                        $json['html'] .= '<p class="list-group-item-text text-muted"><i>NAS порт</i></p>';
                            //                        $json['html'] .= '</div>';

                            if(isset($data['circuit_id'])) {
                                ?>
                                <div class="col-xs-4">
                                <p class="list-group-item-text text-muted"><i>Circuit ID</i></p>
                                <h4 class="list-group-item-heading"><?=$data['circuit_id']?></h4>
                                </div><?
                            }
                            ?></div>
                    </li><?
                    if(isset($data['circuit_id'])) {
//                        if(0) {
//                        if(0) {
//                            Custom PPPoE+ Circuit ID format:
//                           elt-gepon-%MNGIP%-%CHANNELID%-%PATHID%-%ONTID%-%VLAN0%
//                           LTP
//                           elt-gpon-%MNGIP%-%GPON-PORT%-%ONTID%-%VLAN0%
//
//                            elt-sw-%M-%h-%p-%v
//                           %M - Мак адрес системы
//                           %h - hostname
//                           %p - короткое имя порта
//                           %v - индентификатор vlan



                        ?><li class="list-group-item bg-info">
                        <div class="row bg-info"><?
                            //                            $circuit = explode("/",$sessions['sess_data']->circuit_id)
                            ?><div class="col-md-3">
                                <p class="list-group-item-text text-muted"><i>Порт</i></p>
                                <h4 class="list-group-item-heading"><?=$data['circuit_id']?></h4>
                            </div><?
                            if(0) {
//                            if(isset($circuit[1])) {


                                //circuit id разбирается здесь
                                switch ($circuit[1]) {
                                    case "sw":
                                        $brand = $this->brands[$circuit[0]];
                                        $type = $this->types[$circuit[1]];
                                        break;


                                    case "xpon":
                                        $brand = "Eltex";
                                        $type = "PON";
                                        break;

                                    default:
                                        ?><div class="col-md-3">
                                        <p class="list-group-item-text text-muted"><i>Порт</i></p>
                                        <h4 class="list-group-item-heading"><?=$circuit[2]?></h4>
                                        </div>
                                        <div class="col-md-3">
                                            <p class="list-group-item-text text-muted"><i>Порт</i></p>
                                            <h4 class="list-group-item-heading"><?=$circuit[3]?></h4>
                                        </div>
                                        <div class="col-md-3">
                                        <p class="list-group-item-text text-muted"><i>VLAN</i></p>
                                        <h4 class="list-group-item-heading"><?=$circuit[4]?></h4>
                                        </div><?php
                                        break;
                                }
                            }
                            ?><div class="col-md-3"><?

                                //                            $json['html'] .= '<h4 class="list-group-item-heading">' .  $circuit[2]. '</h4>';

                                //circuit id выдается здесь
                                //                            $json['html'] .= '<p class="list-group-item-text text-muted"><i>'.$type.' '.$brand.'</i></p>';
                                ?><p class="list-group-item-text text-muted"><i><?=$data['circuit_id']?></i></p>
                            </div>
                        </div>
                        </li><?
                    }

                    ?><li class="list-group-item"><?
                        if($data['svcs']!='') {
                            $svcs = (array)json_decode($data['svcs']);
                            ?><div class="row"><?

//                        $json['html'] .= var_dump($svcs);

                            //
                            ?><div class="col-xs-4">
                            <h4 class="list-group-item-heading">Сервисы</h4><?
//                            $json['html'] .= '<p class="list-group-item-text text-muted"><i>Сервисы</i></p>';
                            ?></div>
                            <div class="col-xs-8"><?
//                            $json['html'] .= '<h4 class="list-group-item-heading">' .  "</h4>";
                            for ($k = 0; $k < count($svcs); $k++) {
                                ?><p class="list-group-item-text "><?=$svcs[$k]->f1?></i><?
                                $color = '';
                                for($u=0;$u<count($svcs[$k]->f2);$u++){
                                    switch ($svcs[$k]->f2[$u]){
                                        case "S":
                                            $color = "text-success text-bold";
                                            break;

                                        case "A":
                                        case "D":
                                            $color = "text-primary";
                                            break;

                                        case "F":
                                        case "SF":
                                            $color = "text-danger text-bold";
                                            break;
                                    }
                                    ?>&nbsp;<a href="#" data-toggle="tooltip" data-placement="bottom" title="<?=$service_code[$svcs[$k]->f2[$u]]?>" class="<?=$color?>"><?=$svcs[$k]->f2[$u]?></a><?
//                                    $json['html'] .= '&nbsp;<span class="'.$color.'">'.$this->service_auth_code[$svcs[$k]->f2[$u]].'</span>';
                                }
                                ?></p><?
                            }
                            ?></div><?
//                            if ($sessions['services'][$k]->command_code == 'S') {

//                            }
                            //                        $json['html'] .='<h4 class="list-group-item-heading">' . $service_auth_code[$sessions['services'][$k]->command_code]."</h4>";
                            ?></div><?
                        }else{
//                        $json['html'] .= '<h4 class="list-group-item-heading">Сервисы не активны</h4>';
//                            $json['html'] .= '<p class="list-group-item-text text-muted"><i>Нет активных сервисов</i></p>';
                            ?><div class="row"><?

//                        $json['html'] .= var_dump($svcs);

                            //
                            ?><div class="col-xs-4">
                            <h4 class="list-group-item-heading">Сервисы</h4><?
//                            $json['html'] .= '<p class="list-group-item-text text-muted"><i>Сервисы</i></p>';
                            ?></div>
                            <div class="col-xs-8"><?
                            if(isset($data['active_svcs'])){
                                ?><p class="list-group-item-text text-success text-bold "><?= str_replace(["{","}"],"",$data['active_svcs']) ?></i></p><?
                            }else{
                                ?><p class="list-group-item-text text-muted"><i>Нет активных сервисов</i></p><?
                            }

                            ?></div><?
                        }
                        ?></li>
                    </ul><?
                }
                    ?></div><?
                }
            }

            /*?><div role="tabpanel" class="tab-pane <?=$active?>" id="tab<?=$h?>">
            </div><?*/
        } ?>
    </div>

    </div><?

//SiteHelper::debug($accounting);
//SiteHelper::debug($service_code);
//SiteHelper::debug($post);