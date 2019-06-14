<?php
/**
 * Created by PhpStorm.
 * User: asmild
 * Date: 06.08.18
 * Time: 15:13
 */

namespace frontend\modules\tools\controllers;

use common\components\SiteHelper;
use common\models\arps\ArpTables;
use frontend\components\FrontendComponent;
use Symfony\Component\Finder\Iterator\SortableIterator;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;
use common\models\Access;
use common\models\Asterisk;
use common\components\RouterosAPI;
use yii\web\Response;
use yii\widgets\ActiveForm;
use frontend\modules\ipmon\models\Routers;

class VoipController extends FrontendComponent
{
    private $user_id;
    protected $permissions;
    public function beforeAction($action){
        if(!parent::beforeAction($action)){
            return false;
        }
        $this->user_id = $this->cas_user->id;
        $this->permissions['access'] = Access::hasAccess($this->cas_user->id, $this->cas_user->roles, 35); //14 - id доступа к SessMon
        $this->permissions['passwords'] = Access::hasAccess($this->cas_user->id, $this->cas_user->roles, 36); //14 - id доступа к SessMon
        $this->permissions['edit'] = Access::hasAccess($this->cas_user->id, $this->cas_user->roles, 37); //14 - id доступа к SessMon

        if(!$this->permissions['access']){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }
        $this->view->title = "Мониторинг сессий";
        return true;
    }



    private $tau=[
        "TAU-8.IP"=>[
            'ports_count'=>8
        ],
        "TAU-2M.IP"=>[
            'ports_count'=>2
        ]
    ];

    public $acsm = "http://acsm.t72.ru:7557/";

    private function ServerAvailable($domain,$timeout){
        $curlInit = curl_init($domain);
        curl_setopt($curlInit,CURLOPT_CONNECTTIMEOUT,$timeout);
        curl_setopt($curlInit,CURLOPT_HEADER,true);
        curl_setopt($curlInit,CURLOPT_NOBODY,true);
        curl_setopt($curlInit,CURLOPT_RETURNTRANSFER,true);
        $response = curl_exec($curlInit);
        curl_close($curlInit);
        return $response;
    }

    private function LoadDevices($ip = null){
        if(!is_null($ip)){
//http://acsm.t72.ru:7557/devices/?query=%7B%22InternetGatewayDevice.ManagementServer.ConnectionRequestURL%22%3A%22http://10.80.0.3:9998%22%7D
            $ch = curl_init($this->acsm."devices/?query=%7B%22InternetGatewayDevice.ManagementServer.ConnectionRequestURL%22%3A%22http://".$ip.":9998%22%7D"); //'
        }else{
            $ch = curl_init($this->acsm."devices/"); //
        }
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,1); //timeout 1 sec
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $ret = curl_exec($ch);
        curl_close($ch);
        return json_decode($ret);
    }

    private function LoadDeviceInfo($mac = null){
        //http://acsm.t72.ru:7557/devices/?query=%7B%22InternetGatewayDevice.ManagementServer.ConnectionRequestURL%22%3A%22http://10.80.0.3:9998%22%7D'
//        http://acsm.t72.ru:7557/devices/?query=%7B%22InternetGatewayDevice.WANDevice.1.WANConnectionDevice.1.WANIPConnection.1.MACAddress%22%3A%22a8:f9:4b:03:a3:65%22%7D'
        $ch = curl_init($this->acsm."devices/?query=%7B%22InternetGatewayDevice.WANDevice.1.WANConnectionDevice.1.WANIPConnection.1.MACAddress%22%3A%22".$mac."%22%7D"); //
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,1); //timeout 1 sec
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $ret = curl_exec($ch);
        curl_close($ch);

        $data = [];
        $js = json_decode($ret);
        if(!empty($js)){
            $data['lastInform']=strtotime($js[0]->{'_lastInform'});
//                $lastBoot=strtotime($val->{'_lastBoot'});
            $data['product'] = $js[0]->{'_deviceId'}->{'_ProductClass'};
            $data['ports_count'] = $this->tau[$data['product']]['ports_count'];
            $data['serial'] = $js[0]->{'_deviceId'}->{'_SerialNumber'};
            $data['firmware'] = $js[0]->{'InternetGatewayDevice'}->{'DeviceInfo'}->{'SoftwareVersion'}->{'_value'};
            $data['ip'] = explode(":",explode("//", $js[0]->{'InternetGatewayDevice'}->{'ManagementServer'}->{'ConnectionRequestURL'}->{'_value'})[1])[0];
        }
        return $data;
    }

    private function RoutersList($id){
        $routerList = Routers::find()->where(['id'=>$id])->all();
        $data=array();
        foreach ($routerList as $item) {
//            $id = $item -> id;
            $data['ip'] = $item -> ip;
            $data['description'] = $item->description;
            $data['readUser'] = $item->user;
            $data['readPass'] = $item->apipass;
            $data['writeUser'] = $item->rwuser;
            $data['writePass'] = $item->apiwrpass;
            $data['visible'] = $item->visible;
        }
        return $data;
    }


    private function LoadDhcpLeases($network = null){
        $credentials = $this->RoutersList(5);

        $ip = $credentials['ip'];
        $user = $credentials['readUser'];
        $pass = $credentials['readPass'];

        $API = new RouterosAPI();
        $ARRAY = array();


        if ($API->connect($ip, $user, $pass)) {
            $API->write("/ip/dhcp-server/lease/print");
//            $API->write("interface",$int);
            $ARRAY = $API->read();
            $API->disconnect();
        }

        $res = [];

        $ARRAY=ArrayHelper::index($ARRAY,'address');
        foreach ($ARRAY as $ip=>$arr){
            if((isset($arr['server']))&&($arr['server']==$network)){
                $res[$ip] = $arr;
            };
        }
        return $res;
    }

    public function actionValidate()
    {
        $model = new Asterisk();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }

    public function actionIndex($ip = null){
//        $leases = $this->LoadDhcpLeases('10.80.0.0/24');
        $post = Yii::$app->request->post();
//            echo 'eeeee';
//            return $this->redirect('/tools/voip');
//        }
        $gates = [];
        $model = new Asterisk();
        $lastInform = 0;
        $lastBoot   = 0;
        $registered = 0;
        $timestamp  = 0;
//        $ip = null;
        $ipDB = $ip;
        $js = [];
          if(isset($post['Asterisk']['ip'])) {
              return $this->redirect('/tools/voip/?ip='.$post['Asterisk']['ip']);
          }
          if(!is_null($ip)){
            if(strpos($ip,'/')){
                $ipArr = explode('.',$ip);
                $ipArr[3] = '*';
                $ip = implode('.',$ipArr);
                unset($ipArr[3]);
                $ipDB = implode('.',$ipArr);
            }
            if ($this->ServerAvailable($this->acsm, 1)) {
                $js = ($this->LoadDevices($ip));
//            $js->{'_available'}= true;
            } else {
//            $js->{'_available'}= false;
            }
            if (!empty($js)) {
                foreach ($js as $val) {
                    $lastInform = strtotime($val->{'_lastInform'});
//                $lastBoot=strtotime($val->{'_lastBoot'});
                    $unitType = $val->{'_deviceId'}->{'_ProductClass'};
                    $ports_count = $this->tau[$unitType]['ports_count'];
                    $m = [];

                    for ($i = 1; $i <= $ports_count; $i++) {
                        $m[$i] = 0;
                        if (isset($val->{'InternetGatewayDevice'}->{'Services'}->{'VoiceService'}->{'1'}->{'VoiceProfile'}->{'1'}->{'Line'}->{$i}->{'Enable'}->{'_value'})) {
                            $m[$i] = $val->{'InternetGatewayDevice'}->{'Services'}->{'VoiceService'}->{'1'}->{'VoiceProfile'}->{'1'}->{'Line'}->{$i}->{'Enable'}->{'_value'};
                        }
                    }
                    $gates[$val->{'_deviceId'}->{'_SerialNumber'}] = [
                        'product' => $unitType,
                        'firmware' => $val->{'InternetGatewayDevice'}->{'DeviceInfo'}->{'SoftwareVersion'}->{'_value'},
                        'mac' => $val->{'InternetGatewayDevice'}->{'WANDevice'}->{'1'}->{'WANConnectionDevice'}->{'1'}->{'WANIPConnection'}->{'1'}->{'MACAddress'}->{'_value'},
                        'url' => explode(":", explode("//", $val->{'InternetGatewayDevice'}->{'ManagementServer'}->{'ConnectionRequestURL'}->{'_value'})[1])[0],
                        'last' => $lastInform,
                        'm' => $m,
                    ];
                }
            }
//        $dateserv=date("Y-m-d H:i:s", time());
//        $dateserv2=new DateTime($dateserv);
        }
        return $this->render('index',[
//            'data'=>$js,
            'gates'=>$gates,
            'permissions'=>$this->permissions,
            'model'=>$model,
            'user_id'=>$this->user_id,
            'voip_gates'=>ArrayHelper::index((new Query())->select('*')->from('voip_gates')->where("ip LIKE '".$ipDB."%'")->all(),'ip')
        ]);

    }

    public function actionGate(){
//        Yii::$app->getSession()->setFlash('mac', $mac);
        $post = Yii::$app->request->post();
        $data = [];
        if(isset($post['addGate'])){
            if(!isset($post['addGate']['description'])){
                $post['addGate']['description']="";
            }

            $credentials = ArpTables::find()->where(['ip'=>long2ip(3648270415)])->one();  //ip - 217.116.48.79
//            $arp_record =
//            $mac = preg_replace('/(..)(..)(..)(..)(..)(..)/',                    '$1:$2:$3:$4:$5:$6', $post['addGate']['mac']);
               //ARP таблица интерфейса

//            SiteHelper::debug($post['addGate']);
//            SiteHelper::debug($mikrotik_addr);
//            die();

            if(!(new Query())->from('voip_gates')->where(['mac'=>$post['addGate']['mac']])->exists()){
                $mac = preg_replace('/(..)(..)(..)(..)(..)(..)/',                    '$1:$2:$3:$4:$5:$6', $post['addGate']['mac']);
                $data = $this->LoadDeviceInfo($mac);
                //Добавляем статик лизу в DHCP и static ARP
                // server - "10.80.0.0/24"  //переделать на авто подстановку
                $server = '10.80.0.0/24';
                $mikrotik_addr = ArpTables::DhcpStaticLeaseAdd($credentials,$server,$post['addGate']['ip'],$mac);

                //Добавляем в БД
                $insert['description'] = $post['addGate']['description'];
                $insert['model'] = (new Query())->select('id')->from('voip_models')->where(['name'=>$data['product']])->scalar();
                $insert['serial'] = $data['serial'];
                $insert['mac'] = $post['addGate']['mac'];
                $insert['created_at'] = time();
                $insert['ip'] = $data['ip'];
                (new Query())->createCommand()->insert('voip_gates',$insert)->execute();
            }
        };
        return $this->redirect('/tools/voip?ip='.$post['addGate']['ip']);
//        return $this->redirect('/tools/voip?ip='.$data['ip']);
    }

    private function LoadDeviceConfig($mac){
        $ch = curl_init($this->acsm."presets/?query=%7B%22_id%22%3A%22".$mac."%22%7D"); //
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,1); //timeout 1 sec
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        $ret = curl_exec($ch);
        curl_close($ch);

        $js = json_decode( $ret );
        $data=[];
        if(!empty($js)) {
            $config = $js[0]->configurations;
            foreach ($config as $fxs_config) {
                $pos = strpos($fxs_config->name, "Enable");
                if ($pos !== false) {

                    $i = explode(".", explode("Line.", $fxs_config->name)[1])[0];
//                $enchek[$i]=$fxs_config->value;
                    $data[$i]['state'] = $fxs_config->value;
                }

                $pos = strpos($fxs_config->name, "AuthUserName");
                if ($pos !== false) {
                    $i = explode(".SIP", explode("Line.", $fxs_config->name)[1])[0];
//                $atel[$i]=$fxs_config->value;
                    $data[$i]['atel'] = $fxs_config->value;
                }

                $pos = strpos($fxs_config->name, "AuthPassword");
                if ($pos !== false) {
                    $i = explode(".SIP", explode("Line.", $fxs_config->name)[1])[0];
//                $apass[$i]=$fxs_config->value;
                    $data[$i]['apass'] = $fxs_config->value;
                }
            }
        }

        return $data;
    }

    public function actionDevice($mac = null){
        $post = Yii::$app->request->post();
//        SiteHelper::debug($post);
//        die();
        if(isset($post['GateData'])){
            $data = $post['GateData'];
//            SiteHelper::debug($data);
//            die();
            $sys_mac = $data['mac'];
            $ip = $data['ip'];
            $mac = preg_replace('/(..)(..)(..)(..)(..)(..)/',
                '$1:$2:$3:$4:$5:$6', $sys_mac);
            unset($data['mac']);
            unset($data['ip']);
            $n = count($data);
            $data2["weight"]=9;
            $data2["precondition"]="{\"InternetGatewayDevice.WANDevice.1.WANConnectionDevice.1.WANIPConnection.1.MACAddress\":\"".$mac."\"}";
            $k=0;
            $changed=[];

            for ($i = 1; $i <= $n; $i++)
            {
                if($data[$i]["changed"]){
                    if ((empty($data[$i]["pass"])) || ($data[$i]["pass"] == '00' . $i)) {
                        $data[$i]["pass"] = SiteHelper::generateRandomString(20);
                    }


//                    if(isset($data[$i]["state"])){
//                    }

                    if(
                        ((!empty($data[$i]['pass']))||($data[$i]['pass']!='00'.$i))
                        &&
                        ((!empty($data[$i]['ntel']))||($data[$i]['ntel']!='00'.$i))
                    ){
                        if(!isset($data[$i]['context'])){
                            $data[$i]['context'] = 'mobileMG';
                        }
                    }
                    $changed[$i]=$data[$i];
                    Asterisk::VoipInsert($data[$i]['ntel'],$data[$i]['pass'],$data[$i]['context'],3,$data[$i]["device_id"],$this->user_id);
                }

                if(isset($data[$i]["delete"])){
                    $data[$i]["state"] = 0;
                    $data[$i]["ntel"] = "00".$i;
                    $data[$i]["pass"] = "00".$i;
                }

                $data2["configurations"][$k]=[ "type"=>"value", "name"=>"InternetGatewayDevice.Services.VoiceService.1.VoiceProfile.1.Line.".$i.".Enable", "value"=>
                    empty($data[$i]["state"]) ? "0" : $data[$i]["state"] ];
                $k++;
                $data2["configurations"][$k]=[ "type"=>"value", "name"=>"InternetGatewayDevice.Services.VoiceService.1.VoiceProfile.1.Line.".$i.".CallingFeatures.CallerIDName", "value"=>$data[$i]["ntel"] ];
                $k++;
                $data2["configurations"][$k]=[ "type"=>"value", "name"=>"InternetGatewayDevice.Services.VoiceService.1.VoiceProfile.1.Line.".$i.".DirectoryNumber", "value"=>$data[$i]["ntel"] ];
                $k++;
                $data2["configurations"][$k]=[ "type"=>"value", "name"=>"InternetGatewayDevice.Services.VoiceService.1.VoiceProfile.1.Line.".$i.".SIP.AuthUserName", "value"=>$data[$i]["ntel"] ];
                $k++;

               /* if(isset($data[$i]["state"])){
                    if((empty($data[$i]["pass"]))||($data[$i]["pass"]=='00'.$i)){
                        $data[$i]["pass"] = SiteHelper::generateRandomString(20);
                    }
                }else{
                    if((empty($data[$i]["pass"]))||($data[$i]["pass"]=='00'.$i)){
                        $data[$i]["pass"] = SiteHelper::generateRandomString(20);
                        $data[$i]['context'] = 'mobileMG';
                    }
                }
                if($data[$i]["changed"]){
                    if(isset($data[$i]["state"])){
                        $changed[$i]=$data[$i];
                    }
                }*/

//                if(isset($data[$i]["delete"])){
//                    $data[$i]["pass"] = "00".$i;
//                }
                $data2["configurations"][$k]=[ "type"=>"value", "name"=>"InternetGatewayDevice.Services.VoiceService.1.VoiceProfile.1.Line.".$i.".SIP.AuthPassword", "value"=>$data[$i]["pass"] ];
                $k++;

//                $changed = [];
            }

//            SiteHelper::debug($data);
//            SiteHelper::debug($data2);
//            SiteHelper::debug($changed);
//            die();
            Asterisk::UpdateData($changed,$this->user_id);


//            die();
            $data_json2 = json_encode($data2);
            $ch2 = curl_init($this->acsm."presets/".str_replace(":","",$mac));
            curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch2, CURLOPT_POSTFIELDS, $data_json2);
            curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
            $response  = curl_exec($ch2);
            curl_close($ch2);
            Yii::$app->getSession()->setFlash('mac', $sys_mac);
            return $this->redirect('/tools/voip?ip='.$post['GateData']['ip']);
        }

        $data = $this->LoadDeviceConfig($post['mac']);
        $atel = [];
        foreach ($data as $datum){
            if($datum['state']){
                $atel[]=$datum['atel'];
            }
        }
        $sip_accounts = ArrayHelper::index(Asterisk::find()->select('name,context')->where(['name'=>$atel])->asArray()->all(),'name');

        $n=$this->tau[$post['devtype']]['ports_count'];
//        return $ret;
        return $this->renderPartial('device',[
            'data'=>$data,
            'n'=>$n,
            'sip_accounts'=>$sip_accounts,
            'mac'=>$post['mac'],
            'ip'=>$post['ip'],
            'permissions'=>$this->permissions,
            'user_id'=>$this->user_id,
            'device_id'=> $post['deviceId']
        ]);
    }
}