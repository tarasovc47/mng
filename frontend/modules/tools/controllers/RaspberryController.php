<?php
namespace frontend\modules\tools\controllers;
use Yii;
use frontend\components\FrontendComponent;
use common\components\RouterosAPI;
use yii\db\Query;
use \yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use common\models\Access;


class RaspberryController extends FrontendComponent
{
/*
    public function beforeAction($action){
        if(!parent::beforeAction($action)){
            return false;
        }
        $this->permission = Access::hasAccess($this->cas_user->id, $this->cas_user->roles, 10); // 2 - id доступа к админке
//        die();
        if(!$this->permission){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }
        $this->view->title = "Инструменты | Конфигурации терминалов";
        return true;
//        return parent::beforeAction($action);
    }
*/
    public function actionIndex(){
        return $this->render('index');
    }

    public function actionDelete($id){
        $query = new query();
        $query->createCommand()->delete('rpis',['id'=>$id])->execute();
        return $this->redirect('/tools/raspberry/');
//        echo $id;
    }

    public function actionAjax(){
        $post = Yii::$app->request->post();
//        print_r($post);
        $json = array();
        $json['html'] = '';
        if (1) {
            switch ($post['action']){
                case "update":
                    ////Добавить UPDATE
                     Yii::$app->db->createCommand()
                    ->update('rpis', [
                    'config' => $post['config'],
                    'user' => $post['user'],
                    ],[
                        'mac' => $post['mac'],
                        'ip' => $post['ip']
                    ])
                    ->execute();

                    print_r($post);
                    break;

                case "show_terms":
                    $query = new Query();
                    $query_cas = new Query();
                    $query->select('*')->from('rpis');//->where(["id"=>($query_rid->all())[0]['router_id']]);
                    $query_cas->select('first_name,last_name,login')->from('cas_user')->orderBy("first_name");
                    $tmp = $query_cas->all();
                    for($i=0;$i<count($tmp);$i++){
                        if(isset($tmp[$i]['login'])){
                            $fullUserName[$tmp[$i]['login']] = $tmp[$i]['first_name']." ".$tmp[$i]['last_name'];
                        }
                    }
                    $fullUserName['unknown'] = 'unknown';
                    unset($tmp);
                    $table = [];
                    //->where(["id"=>($query_rid->all())[0]['router_id']]);
                    $rpis = $query->all();
                    $rows = "";
                    for($i=0;$i<count($rpis);$i++){
                        if((isset($rpis[$i]['user']))&&($rpis[$i]['user']!='')){
                            $user = $rpis[$i]['user'];
                        }else{
                            $user = 'unknown';
                        }

//                        $json['ate'] =  $fullUserName;

                        $server = trim(explode(" = ",explode("\n",$rpis[$i]['config'])[0])[1]);

                        $rows = "<tr>";
//                        $server = explode("\n",$rpis[$i]['config'])[0];
//                        $rows .= "<td>".$rpis[$i]['mac']."</td><td>".$rpis[$i]['ip']."</td><td>".isset($fullUserName[$user])?$fullUserName[$user]:"none"."</td><td>
//<a  onclick='RPiInfo(\"".$rpis[$i]['id']."\")'><i class='fa fa-info-circle fa-fw'></i></a>&nbsp;
//<a class='edit' id='".$rpis[$i]['id']."'><i class='fa fa-edit fa-fw'></i></a></td>";
$rows .= "<td>".$rpis[$i]['mac']."</td><td>".$rpis[$i]['ip']."</td><td>none</td><td>
<a  onclick='RPiInfo(\"".$rpis[$i]['id']."\")'><i class='fa fa-info-circle fa-fw'></i></a>&nbsp;
<a class='edit' id='".$rpis[$i]['id']."'><i class='fa fa-edit fa-fw'></i></a></td>";


                        $rows .= "</tr>";
                        $rows .= "<tr>";
                        $rows .= "</tr>";
                        $table[$server][] = [
                            "mac"=>$rpis[$i]['mac'],
                            "ip"=>$rpis[$i]['ip'],
                            "user"=>isset($fullUserName[$user])?$fullUserName[$user]:"none",
                            "id"=>$rpis[$i]['id']
                        ];
                    }

                    foreach ($table as $server=>$rpi_data){
                        $json['html'] .= "<div class='col-md-6'>";
                        $json['html'] .= "<div class='panel panel-default'>";
                        $json['html'] .= "<div class='panel-heading'><b>".$server."</b></div>";
                        $json['html'] .= "<table class='table table-condensed table-hover'>";
                        $json['html'] .= "<tr><thead><th>IP/<font size='2'><span class='text-muted'>MAC</span></font></th><th>User</th><th>&nbsp;</th></thead></tr>";
                        for($i=0;$i<count($rpi_data);$i++){
                            $json['html'] .= "<tr>";
                            $json['html'] .= "<td><a target='_blank' href='http://".$rpi_data[$i]['ip']."'>".$rpi_data[$i]['ip']."</a><br>";
                            $json['html'] .= "<font size='2'><span class='text-muted'>".$rpi_data[$i]['mac']."</span></font></td>";
                            $json['html'] .= "<td>".$rpi_data[$i]['user']."</td>";
                            $json['html'] .= "<td><div class='btn-group' role='group'>
<a class='btn btn-sm btn-info' onclick='RPiInfo(\"".$rpi_data[$i]['id']."\")'><i class='fa fa-info-circle fa-fw'></i></a>&nbsp;
<a class='btn btn-sm btn-warning' id='".$rpi_data[$i]['id']."' onclick='EditRPi(".$rpi_data[$i]['id'].")'><i class='fa fa-edit fa-fw'></i></a>&nbsp;
<a class='btn btn-sm btn-danger' target='_blank' href='http://".$rpi_data[$i]['ip']."/reboot?' title='Перезагрузка устройства'><i class='fa fa-refresh fa-fw'></i></a><a class='btn btn-sm btn-default' onclick='if(confirm(\"Удалить?\")){ return true; }else{return false;};' href='/tools/raspberry/delete?id=".$rpi_data[$i]['id']."' title='Удалить из БД' data-toggle='tooltip'><i class='fa fa-remove'></i></a>
</div></td>";
                            $json['html'] .= "</tr>";
                        }
                        $json['html'] .= "</table>";
                        $json['html'] .= "</div>";
                        $json['html'] .= "</div>";
                        $json['html'] .= "</div>";


                    }

//                    $json['html'] .= json_encode($table);
                    $json['ans'] = $rpis;
                    $json['html'] .= "<script>$('.edit').on('click',function () {
    EditRPi($(this).attr('id'));
})</script>";
                    break;

                case "show_conf":
                    $json = array();
                    $config = array();
                    $json['html'] = '';
                    $query = new Query();

                    $query_cas = new Query();
                    $query->select('*')->from('rpis')->where(["id"=>$post['id']]);
                    $rpi = $query->all();
                    $config_tmp = explode("\n", $rpi[0]['config']);
                    for($i=0;$i<count($config_tmp)-1;$i++){
                        $tmp = explode(" = ",$config_tmp[$i]);
                        $config[$tmp[0]]=$tmp[1];
                    }
                    $tmp = array();
                    $query_cas->select('first_name,last_name,login')->from('cas_user')->orderBy("first_name");
                    $tmp = $query_cas->all();
                    if($rpi[0]['user']!=''){
                        if($tmp[0]['login']==$rpi[0]['user']){
                            $username = $tmp[0]['first_name']." ".$tmp[0]['last_name'];
                        }else{
                            $username = $rpi[0]['user'];
                        }
                    }else{
                        $username = "unknown";
                    }
                    $json['html'] .= "<div class='panel panel-info'><div class='panel-heading'><b>Конфигурация RPi ".$rpi[0]['ip']."</b> ".$rpi[0]['mac']."</div><div class='panel-body'>";
                    $json['html'] .= "<textarea rows='10' style='resize: none' readonly class='form-control'>".$rpi[0]['config'];

                    /*foreach ($config as $param => $value){
                        $json['html'] .='<div class="form-group">
    <label for="'.$param.'">'.$param.'</label>
    <input type="text" class="form-control" id="'.$param.'" value="'.$value.'">
  </div>';
                    }*/
//                    $json['html'] .= "<textarea>".json_encode($config);
                    $json['html'] .= "</textarea>";
                    $json['html'] .= "</div></div>";
                    $json['users'] = '<option></option>';
                    $json['users_raw'] = $tmp;
                    for($f=0;$f<count($tmp);$f++){
                        $selected = "";
                        if($tmp[$f]['login'] == $rpi[0]['user']){
                            $selected = "selected";
                        }
                        $json['users'] .='<option '.$selected.' value="'.$tmp[$f]['login'].'">'.$tmp[$f]['first_name']." ".$tmp[$f]['last_name'].'</option>';
                    }
//                    $json['user'] = $rpi[0]['user'];
                    $json['ip'] = $rpi[0]['ip'];
                    $json['mac'] = $rpi[0]['mac'];
                    $json['config'] = $rpi[0]['config'];
                    break;


            }

//        if (Yii::$app->request->isAjax) {
        }
        return json_encode($json);
    }
}
