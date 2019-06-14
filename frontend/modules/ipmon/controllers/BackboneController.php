<?php
namespace frontend\modules\ipmon\controllers;
use common\components\SiteHelper;
use common\models\backbone\BackboneHosts;
use common\models\backbone\BackboneNodes;
use common\models\backbone\BackboneVlans;
use frontend\components\FrontendComponent;
use yii\db\Query;
use common\components\EltexSnmpAPI;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use common\components\RouterosAPI;
use Yii;
use common\models\Access;
use yii\web\ForbiddenHttpException;
use yii\widgets\ActiveForm;
use yii\web\Response;



class BackboneController extends FrontendComponent
{
    /*public function beforeAction($action){
        $this->view->title = "IpMon | Опорная сеть: ARP";
        return parent::beforeAction($action);
    }*/
    private $router = 5; // ID роутера
    private $debug = 1;
    private $community = "sysadmin";
    private $permission;

    private function DHCPlease($id,$server,$ip,$mac){
        $id = $this->router; //ID router 5

        $query_router = new Query();
        $query_router->select('ip,rwuser,apiwrpass')->from('routers')->where(["id"=>$id]);
        $credentials = ($query_router->all())[0];

        $credip = $credentials['ip'];
        $user = $credentials['rwuser'];
        $pass = $credentials['apiwrpass'];
        $ARRAY = array();
        $API_wr = new RouterosAPI();
        if ($API_wr->connect($credip, $user, $pass)) {
            $ARRAY = $API_wr->comm("/ip/dhcp-server/lease/add", array(
                "mac-address" => $mac,
                "address" => $ip,
                "server" => $server
            ));
            $API_wr->disconnect();
        }
        return $ARRAY;
    }

    private function LoadDHCPLeases($id){
        $query_router = new Query();
        $query_router->select('ip,rwuser,apiwrpass')->from('routers')->where(["id"=>$id]);
        $credentials = ($query_router->all())[0];
        $credip = $credentials['ip'];
        $user = $credentials['rwuser'];
        $pass = $credentials['apiwrpass'];

        $API = new RouterosAPI();
//        $API->debug = true;
        $ARRAY = array();
        if ($API->connect($credip, $user, $pass)) {
            $API->write("/ip/dhcp-server/lease/print");
//            $API->write("interface",$int);
            $ARRAY = $API->read();
            $API->disconnect();
        }
        return $ARRAY;
    }

    private function RemoveDHCPLease($id,$addr){
        $id = $this->router;
        $query_router = new Query();
        $query_router->select('ip,rwuser,apiwrpass')->from('routers')->where(["id"=>$id]);
        $credentials = ($query_router->all())[0];
        $credip = $credentials['ip'];
        $user = $credentials['rwuser'];
        $pass = $credentials['apiwrpass'];
        $numbers=[];
        $tmp1 = $this->LoadDHCPLeases($id);
        for($i=0;$i<count($tmp1);$i++){
            if($tmp1[$i]['address']==$addr){
                $numbers =  $tmp1[$i]['.id'];
            }
        }
        $API = new RouterosAPI();

//        $API->debug = true;
        $ARRAY = array();
        if ($API->connect($credip, $user, $pass)) {
            $ARRAY = $API->comm("/ip/dhcp-server/lease/remove",array(
                "numbers" => $numbers
            ));
            $API->disconnect();
        }
        return $ARRAY;
    }

    public function beforeAction($action){
        if(!parent::beforeAction($action)){
            return false;
        }
//        $this->permission['change_state'] = Access::hasAccess($this->cas_user->id, $this->cas_user->roles, 8);
        $this->permission['r_switches'] = Access::hasAccess($this->cas_user->id, $this->cas_user->roles, 19); //доступ к старнице, добавление нод
        $this->permission['change_state'] = Access::hasAccess($this->cas_user->id, $this->cas_user->roles, 22); //разрешение привязки мак адресов
        $this->permission['not_active'] = Access::hasAccess($this->cas_user->id, $this->cas_user->roles, 28); //2 можно видеть на активные ноды и выводить их в активные, 1 - только просмотр, 0 - не показвать
        $this->permission['vlan'] = Access::hasAccess($this->cas_user->id, $this->cas_user->roles, 29); //0 не увидеть vlan 1 - только просмотр, 2 - можно редактировать
        $this->permission['config'] = Access::hasAccess($this->cas_user->id, $this->cas_user->roles, 4); //Доступ автоконфигурированию
        $this->permission['replace'] = Access::hasAccess($this->cas_user->id, $this->cas_user->roles, 30); //Доступ автоконфигурированию

        if(!$this->permission['r_switches']){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }
        $this->view->title = "IpMon | Опорная сеть";
        return true;
    }

    protected function translit2($s) {
        $s = (string) $s; // преобразуем в строковое значение
        $s = strip_tags($s); // убираем HTML-теги
        $s = str_replace(array("\n", "\r"), " ", $s); // убираем перевод каретки
        $s = preg_replace("/\s+/", ' ', $s); // удаляем повторяющие пробелы
        $s = trim($s); // убираем пробелы в начале и конце строки
        $s = function_exists('mb_strtolower') ? mb_strtolower($s) : strtolower($s); // переводим строку в нижний регистр (иногда надо задать локаль)
        $s = strtr($s, array('а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'j','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shch','ы'=>'y','э'=>'e','ю'=>'yu','я'=>'ya','ъ'=>'','ь'=>''));
        $s = preg_replace("/[^0-9a-z-_ ]/i", "", $s); // очищаем строку от недопустимых символов
        $s = str_replace(" ", "", $s); // Убираем пробелы
        return $s; // возвращаем результат
    }

    protected function translit($string){
        $converter = array(
            'а' => 'a',   'б' => 'b',   'в' => 'v',
            'г' => 'g',   'д' => 'd',   'е' => 'e',
            'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
            'и' => 'i',   'й' => 'y',   'к' => 'k',
            'л' => 'l',   'м' => 'm',   'н' => 'n',
            'о' => 'o',   'п' => 'p',   'р' => 'r',
            'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'h',   'ц' => 'c',
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
            'ь' => '',  'ы' => 'y',   'ъ' => '',
            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

            'А' => 'A',   'Б' => 'B',   'В' => 'V',
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
            'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
            'И' => 'I',   'Й' => 'Y',   'К' => 'K',
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',
            'О' => 'O',   'П' => 'P',   'Р' => 'R',
            'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
            'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
            'Ь' => '',  'Ы' => 'Y',   'Ъ' => '',
            'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
        );
        $tmp = str_replace(" ","",strtr($string, $converter));
        $tmp = str_replace("/","_",$tmp);
        $tmp = str_replace('\\',"_",$tmp);
        $tmp = str_replace('.',"",$tmp);
        return $tmp;
    }

    public function actionValidate($type)
    {
        switch ($type){
            case 'vlan':
                $model = new BackboneVlans();
                if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ActiveForm::validate($model);
                }
                break;

            case 'node':
                $model = new BackboneNodes();
                if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ActiveForm::validate($model);
                }
                break;

            case 'host':
                $model = new BackboneHosts();
                if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ActiveForm::validate($model);
                }
                break;

            case 'lhost':
                $model = new BackboneHosts(['scenario' => 'create']);
                if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ActiveForm::validate($model);
                }
                break;
        }
    }

    public function actionVlan($node=null,$vlan=null){
        $permissions = $this->permission;
        $post = Yii::$app->request->post();
        $model = new BackboneVlans();
        if ($permissions['vlan']>1) {
            $list['node'] = $node;
            $list['nodeIp'] = BackboneNodes::findOne($node)['ip'];
            if($vlan!=null){$list['db_vlan'] = BackboneVlans::LoadVlan($node, $vlan);}else{$list['db_vlan']=0;}

//            $list['db_vlan'] = BackboneVlans::findOne($vlan);
            $template = 'vlan';
        }else{

            $template = 'no_access';
        }
        return $this->render($template, compact('permissions', 'list', 'vlan', 'model'));
    }

    private function SaveNode($data){
        if(BackboneNodes::find()->where(['ip'=>$data['ip']])->exists()){
            $model = BackboneNodes::findOne(['ip'=>$data['ip']]);
            if(!$data['active']){
                $model->umount_date = date('Y-m-d H:i:s');
            }else{
                $model->umount_date = null;
            }
//            $model->ip = $data['ip'];
        }else {
            $model = new BackboneNodes();
            $model->mount_date = date('Y-m-d H:i:s');
            $model->ip = $data['ip'];
        }
        $model->mac = $data['mac'];
        $model->ip = $data['ip'];
        $model->ipmon_id = $data['ipmon_id'];
        $model->community = $data['community'];
        $model->description = $data['description'];
        $model->active = $data['active'];
        $model->man_vlan = $data['man_vlan'];
        $model->node_model = $data['node_model'];

//        if ($model->save(false)) {
//            $ans = 'Ok';
//        } else {
//            $ans = 'Err';
//        }

        if($data['active']) {
            if ($model->save(false)) {
                $ans = 'Ok';
            } else {
                $ans = 'Err';
            }
        }else{
            if ($model->delete()) {  ///Возможно нужно удалить все хость и vlan с этого node
                $ans['db'] = 'Ok';
            } else {
                $ans['db'] = 'Err';
            }
        }

        return $ans;
    }

    private function SaveVlan($data){
        if(BackboneVlans::find()->where(['vlan'=>$data['vlan'],'backbone_node_id'=>$data['backbone_node_id']])->exists()){
            $model = BackboneVlans::findOne(['vlan'=>$data['vlan'],'backbone_node_id'=>$data['backbone_node_id']]);
            if(!$data['active']){
                $model->destroy_date = date('Y-m-d H:i:s');
            }else{
                $model->destroy_date = null;
            }
        }else {
            $model = new BackboneVlans();
            $model->create_date = date('Y-m-d H:i:s');
        }
        $model->backbone_node_id = $data['backbone_node_id'];
        $model->vlan = $data['vlan'];
        $model->network = $data['network'];
        $model->description = $data['description'];
        $model->active = $data['active'];

        if($data['active']) {
            if ($model->save(false)) {
                $ans = 'Ok';
            } else {
                $ans = 'Err';
            }
        }else{
            if ($model->delete()) {  ///Возможно нужно удалить все хость с этим vlan
                $ans['db'] = 'Ok';
            } else {
                $ans['db'] = 'Err';
            }
        }
        return $ans;
    }

    private function SaveHost($data){
        if($this->permission['change_state']>1) {
            $api = new EltexSnmpAPI();
            $exist = '';
            $network = BackboneVlans::findOne($data['vlan_id'])['network'];
            $vlan = BackboneVlans::findOne($data['vlan_id'])['vlan'];
            $router = 1;

            if (BackboneHosts::find()->where(['vlan_id' => $data['vlan_id'], 'ip' => ip2long($data['ip'])])->exists()) {
                $model = BackboneHosts::findOne(['vlan_id' => $data['vlan_id'], 'ip' => ip2long($data['ip'])]);
                $exist = BackboneHosts::find()->where(['vlan_id' => $data['vlan_id'], 'ip' => ip2long($data['ip'])]);
                if (!$data['active']) {
                    $model->umount_date = date('Y-m-d H:i:s');
                } else {
                    $model->umount_date = null;
                }
            } else {

//                $ans['ddd'] = $network;
//                $router = ($query_rid->all())[0]['router_id'];

//                $server="10.70.0.0/23";
//

                $model = new BackboneHosts(['scenario' => 'create']);
//                $model->scenario="create";
                $model->mount_date = date('Y-m-d H:i:s');
            }



            $model->vlan_id = $data['vlan_id'];
            $model->ip = ip2long($data['ip']);
            $model->mac = $data['mac'];
            $model->description = $data['description'];
            if((isset($data['configured']))&&($data['configured']==1)){
                $model->configured = $data['configured'];
//                $ans['test'] = file_get_contents("http://10.60.248.21/dstr_config.php?ip=".$data['ip']."&mac=".$data['mac']."&network=".$network."&model=".$data['sw_model'].'&vlan='.$vlan."&desc=".str_replace(" ","_",$data['description']));
                $ans['test'] = file_get_contents("http://10.60.248.21/dstr_config.php?ip=".$data['ip']."&mac=".$data['mac']."&network=".$network."&model=".$data['sw_model'].'&vlan='.$vlan."&desc=".$this->translit($data['description']));
            }

            if(isset($data['sw_model'])){
                $model->sw_model = $data['sw_model'];
            }


            if ((isset($data['active'])) && ($data['active'] == '1')) {
//                    MakeStatic($switch,$ip,$mac,$vlan)
                if($model->active != $data['active']){
                    $api->MakeStatic(long2ip($data['nodeIP']), $data['ip'], $data['mac'], $data['vlan']);
                    $this->DHCPlease($router,$network,$data['ip'],strtoupper($data['mac']));
//                    НАДО ПРОВЕРИТЬ!
//                    $this->IpArpStaticWrite($router,$network,$data['ip'],strtoupper($data['mac']));
                    $ans['snmp']=$exist;
                    $ans['snmp']='s';
                }
                $model->active = $data['active'];
                if ($model->save(false)) {
                    $ans['db'] = 'Ok';
                } else {
                    $ans['db'] = 'Err';
                }
            } else {
                if($model->active != $data['active']) {
                    $api->RemoveStatic(long2ip($data['nodeIP']), $data['ip'], $data['mac'], $data['vlan']);
                    $this->RemoveDHCPLease(1,$data['ip']);
                    $ans['snmp']='d';
                }

                if ($model->delete()) {
                    $ans['db'] = 'Ok';
                } else {
                    $ans['db'] = 'Err';
                }
            }

            $ans['perm'] = "Allow";
        }else{
            $ans['perm'] = "Deny";
        }

        return $ans;
    }

    public function actionHost($node,$vlan,$host){
//        SiteHelper::debug($_POST);
        $permissions = $this->permission;
        $sss=0;
        $post = Yii::$app->request->post();

        $template = 'host';
        $model = new BackboneHosts();

        $nodeIP = BackboneNodes::findOne($node)['ip'];
        $vid = BackboneVlans::findOne($vlan)['vlan'];
        $list = [
          'node'=> $node,
          'nodeIP'=> BackboneNodes::findOne($node)['ip'],
          'vlan'=> $vid,
          'vlanID'=>$vlan,
          'hostIP'=>$host,
        ];
        $list['post'] = $post;
        $db_host = BackboneHosts::LoadHost($list);

        $list['models'] = BackboneHosts::LoadModels();
        $list['data'] = $db_host;
        return $this->render($template, compact('permissions','model','list'));
    }

    public function actionRelocate(){
//        if (1){
         if (Yii::$app->request->isAjax){
            $list = ArrayHelper::index((BackboneNodes::LoadList())['nodes'],'id');
            $post = Yii::$app->request->post();
            return $this->renderPartial('relocate',[
                    'nodeList'=>$list,
                    'data'=>$post
                     ]);
        }
        return "error";
    }

    private function RelocateHost($array){
        //Удаляем со старого опорного коммутатора
        //Удаляем lease
        //Добавляем на новый опорник
        //Создаем новую lease
        //Update в БД
        $router = 1;
        /*
                 * Array
                    (
                        [ip] =&gt; 172386562
                        [mac] =&gt; E0:D9:E3:AA:07:80
                        [node] =&gt; 11
                        [vlan] =&gt; 2222
                        [new_node] =&gt; 12
                    )
                 */
        $node = BackboneNodes::findOne($array['node']);
        $node_new = BackboneNodes::findOne($array['new_node']);

        $arp = BackboneNodes::LoadARP($array['new_node']);


//        $network = BackboneVlans::findOne($data['vlan_id'])['network'];
//        $vlan = BackboneVlans::findOne($data['vlan_id'])['vlan'];
        foreach ($arp['data'][$array['vlan']]['hosts'] as $ip=>$host_data){
            if(empty($host_data)){
                $newIP = $ip;
                break;
            }
        }

//        die();
        $api = new EltexSnmpAPI();

        $api->RemoveStatic(long2ip($node['ip']), long2ip($array['ip']), $array['mac'], $array['vlan']);  //Удаление ARP из опорного
        $this->RemoveDHCPLease(1,long2ip($array['ip']));  //Освобождаем лизу
        //Вытаскиваем данные по новому опорнику

        $vlanData = BackboneVlans::find()->where(['backbone_node_id'=>$array['node']])->one();
        $vlanDataNew = BackboneVlans::find()->where(['backbone_node_id'=>$array['new_node']])->one();

        if (BackboneHosts::find()->where(['vlan_id' => $vlanData['id'], 'ip' => $array['ip']])->exists()) {
            $model = BackboneHosts::findOne(['vlan_id' => $vlanData['id'], 'ip' => $array['ip']]);
            $model->ip = $newIP;
            $model->vlan_id = $vlanDataNew['id'];
            $model->save(false);
        }
//
        $api->MakeStatic(long2ip($node_new['ip']), long2ip($newIP), $array['mac'], $array['vlan']);  //Добавляем на целевой опорный
        $this->DHCPlease($router,$vlanDataNew['network'],long2ip($newIP),strtoupper($array['mac']));


    }

    public function actionNode($node){
        $permissions = $this->permission;
        $post = Yii::$app->request->post();
        $list['nodeIp'] = BackboneNodes::findOne($node)['ip'];
        if ($permissions['r_switches']>0) {
//            if(isset($post['static'])){
//                if($permissions['change_state']>1){
//
//                    $list['post'] = $this->SaveHost($post['static']);
//                }
//            }

            $relocate_vlan=null;
            if(isset($post['Relocate'])){
                $relocate_vlan=$post['Relocate']['vlan'];
                $this->RelocateHost($post['Relocate']);
            }

            if (isset($post['BackboneVlans'])) {
                if ($permissions['vlan']>1) {
                    $list['post'] = $this->SaveVlan($post['BackboneVlans']);
                }
            }

            if(isset($post['BackboneHosts'])){
                if($permissions['change_state']>1){
//                    $api = new EltexSnmpAPI();

                   /* if($post['BackboneHosts']['active']=='1'){
                        $list['msg']='makestatic';

//                    MakeStatic($switch,$ip,$mac,$vlan){
                        $api->MakeStatic(long2ip($list['nodeIp']),$post['BackboneHosts']['ip'],$post['BackboneHosts']['mac'],$post['BackboneHosts']['vlan']);
                    }else{
                        $list['test']='makedynamic';
                        $api->RemoveStatic(long2ip($list['nodeIp']),$post['BackboneHosts']['ip'],$post['BackboneHosts']['mac'],$post['BackboneHosts']['vlan']);
                    }*/
                    if(!isset($post['BackboneHosts']['active'])){
                        $post['BackboneHosts']['active'] = 0;
                    }
                    $post['BackboneHosts']['nodeIP'] = $list['nodeIp'];
                    $list['answer'] = $this->SaveHost($post['BackboneHosts']);
                }
            }
            $list['node'] = $node;
            $list['post'] = $post;
            $list['arp'] = BackboneNodes::LoadARP($node);
            $model = new BackboneHosts();
            $template = 'arplist';

        }else{
            $template = 'no_access';
        }

        return $this->render($template, compact('permissions', 'list','vlan','model','relocate_vlan'));
    }

    public function actionChecknode(){
        if (Yii::$app->request->isAjax){
            $post = Yii::$app->request->post();
            $id = $post['node_id'];
            $node_data = BackboneNodes::CheckNode($id);
            return json_encode($node_data);
        }
    }

    public function actionIndex($node = null,$vlan = null){
        $list = BackboneNodes::LoadList();
        $list['snmp_models'] = BackboneHosts::LoadModelsByOIDs();
        $list['models'] = BackboneHosts::LoadModels();
        $permissions = $this->permission;
        $template = 'index';
        return $this->render($template, compact('permissions', 'list','vlan','model'));
    }

    public function actionEditnode($node=null){
        $permissions = $this->permission;
        if($this->permission['r_switches']>1) {
            $post = Yii::$app->request->post();
            if (isset($post['BackboneNodes'])) {
                $post['BackboneNodes']['ip'] = ip2long($post['BackboneNodes']['ip']);
                $list['post'] = $this->SaveNode($post['BackboneNodes']);
                return $this->redirect(['/ipmon/backbone/']);
            }else{
                $list['post'] = "Err";
            }
            $nodeData=[];
            $node_model = [];
            $model = new BackboneNodes();
            if($node!=null) {
                $nodeData = BackboneNodes::findOne($node);
                $node_model['snmp'] = EltexSnmpAPI::SwModel(long2ip($nodeData['ip']),$nodeData['community']);
            }
            $template = 'node';
        }else{
            $template = 'no_access';
        }
        $node_model['models'] = BackboneHosts::LoadModelsSnmp();
        return $this->render($template, compact('permissions','nodeData', 'model','node_model'));
    }
}