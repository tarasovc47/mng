<?php
namespace frontend\modules\tools\controllers;
use common\models\arps\ArpTables;
use Yii;
use frontend\components\FrontendComponent;
use common\components\RouterosAPI;
use yii\db\Query;
use \yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use common\models\Access;
use common\components\SiteHelper;


class SwconfController extends FrontendComponent
{
//    public $permission;
//    public function beforeAction($action){
//        if(!parent::beforeAction($action)){
//            return false;
//        }
//        $this->permission = Access::hasAccess($this->cas_user->id, $this->cas_user->roles, 4); // 2 - id доступа к админке
//       33 die();
//        if(!$this->permission){
//            throw new ForbiddenHttpException('Нет доступа');
//            return false;
//        }
//        $this->view->title = "Инструменты | Автоконфигурация коммутаторов";
//        return true;
//       333return parent::beforeAction($action);
//    }

    private function cidr2network($ip, $cidr)
    {
        $network = long2ip((ip2long($ip)) & ((-1 << (32 - (int)$cidr))));

        return $network;
    }

    private function cidr2netmask($cidr)
    {
        $bin='';
        for( $i = 1; $i <= 32; $i++ )
            $bin .= $cidr >= $i ? '1' : '0';

        $netmask = long2ip(bindec($bin));

        if ( $netmask == "0.0.0.0")
            return false;

        return $netmask;
    }

    private function cidr2range($cidr) {
        $cidr = explode('/', $cidr);
        $range_start = ip2long($cidr[0]);
        $range_end = $range_start + pow(2, 32-intval($cidr[1])) - 1;
        return [$range_start, $range_end];
    }


    private function GetFullArp($id){
        $query_router = new Query();
        $query_router->select('ip,rwuser,apiwrpass')->from('routers')->where(["id"=>$id]);
        $credentials = ($query_router->all())[0];
        $ip = $credentials['ip'];
        $user = $credentials['rwuser'];
        $pwd = $credentials['apiwrpass'];
        $API = new RouterosAPI();
//        $API->debug = true;
        $ARRAY = array();
        if ($API->connect($ip, $user, $pwd)) {
            $ARRAY=$API->comm("/ip/arp/print",[ ".proplist"=> "address,mac-address,interface","?interface"=>"sfp-sfpplus1_2221"]);
//            $API->write("interface",$int);
//            $ARRAY = $API->read();
            $API->disconnect();
        }
        return $ARRAY;
    }

    private function RemoveDHCPLease($id,$addr){
        $query_router = new Query();
        $query_router->select('ip,rwuser,apiwrpass')->from('routers')->where(["id"=>$id]);
        $credentials = ($query_router->all())[0];
        $ip = $credentials['ip'];
        $user = $credentials['rwuser'];
        $pwd = $credentials['apiwrpass'];
        $API = new RouterosAPI();
//        $API->debug = true;
        $ARRAY = array();
        if ($API->connect($ip, $user, $pwd)) {
            $ARRAY = $API->comm("/ip/dhcp-server/lease/remove",array(
                "numbers" => $addr
            ));
            $API->disconnect();
        }
        return $ARRAY;
    }

    private function GetDHCPLeases($id){
        $query_router = new Query();
        $query_router->select('ip,rwuser,apiwrpass')->from('routers')->where(["id"=>$id]);
        $credentials = ($query_router->all())[0];
        $ip = $credentials['ip'];
        $user = $credentials['rwuser'];
        $pwd = $credentials['apiwrpass'];

        $API = new RouterosAPI();
//        $API->debug = true;
        $ARRAY = array();
        if ($API->connect($ip, $user, $pwd)) {
            $API->write("/ip/dhcp-server/lease/print");
//            $API->write("interface",$int);
            $ARRAY = $API->read();
            $API->disconnect();
        }
        return $ARRAY;
    }

    private function IpArpRemoveOne($id,$addr){
        $query_router = new Query();
        $query_router->select('ip,rwuser,apiwrpass')->from('routers')->where(["id"=>$id]);
        $credentials = ($query_router->all())[0];
        $ip = $credentials['ip'];
        $user = $credentials['rwuser'];
        $pwd = $credentials['apiwrpass'];

        $API = new RouterosAPI();
//        $API->debug = true;
        $ARRAY = array();
        if ($API->connect($ip, $user, $pwd)) {
            $ARRAY = $API->comm("/ip/arp/remove",array(
                "numbers" =>$API->comm("/ip/arp/print",array(
                    "?address" => $addr
                ))[0]['.id']
            ));
            $API->disconnect();
        }
        return $ARRAY;

    }


    private function GetAddresses($id){
        $query_router = new Query();
        $query_router->select('ip,rwuser,apiwrpass')->from('routers')->where(["id"=>$id]);
        $credentials = ($query_router->all())[0];
        $ip = $credentials['ip'];
        $user = $credentials['rwuser'];
        $pwd = $credentials['apiwrpass'];

        $API = new RouterosAPI();
//        $API->debug = true;
        $ARRAY = array();
        if ($API->connect($ip, $user, $pwd)) {
            $API->write("/ip/address/print");
//            $API->write("interface",$int);
            $ARRAY = $API->read();
            $API->disconnect();
        }
        return $ARRAY;
    }

    private function IpArpStaticWrite($id,$interface,$ip,$mac){
        $query_router = new Query();
        $query_router->select('ip,rwuser,apiwrpass')->from('routers')->where(["id"=>$id]);
        $credentials = ($query_router->all())[0];

        $credip = $credentials['ip'];
        $user = $credentials['rwuser'];
        $pass = $credentials['apiwrpass'];

        $ARRAY = array();
        $API_wr = new RouterosAPI();
        if ($API_wr->connect($credip, $user, $pass)) {
            $ARRAY = $API_wr->comm("/ip/arp/add", array(
                "mac-address" => $mac,
                "address" => $ip,
                "interface" => $interface
            ));
            $API_wr->disconnect();
        }
        return $ARRAY;
    }

    private function DHCPlease($id,$server,$ip,$mac){
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

    public function actionIndex(){
        unset($query_vlan);
        return $this->render('index');
    }

    public function actionAjax(){
        if (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();
            switch($post['play']){
                case "GetVlans":
                    $query_vlan = new Query();
                    $query_vlan->select('*')->from('swconf_vlans')->where(['active'=>true]);
                    $vlans = ($query_vlan->all());
                    $html = "<option></option>";
                    for($i=0;$i<count($vlans);$i++){
                        $html .= "<option value='".$vlans[$i]['id']."'>".$vlans[$i]['vlan']." [".$vlans[$i]['description']."]</option>";
                    }
                    echo json_encode(['html'=>$html]);
                    break;

                case "RemoveLease":
                    $numbers = 0;
                    $row = $post['ip'];
                    $routerData = array();
                    $tmp1 = explode(" ",$row);
                    for($i=0;$i<count($tmp1);$i++){
                        $tmp2 = explode(":",$tmp1[$i]);
                        $routerData[$tmp2[0]]=$tmp2[1];
                    }
                    unset($tmp1);
                    unset($tmp2);
                    $router = $routerData['vlan'];
                    $tmp1 = $this->GetDHCPLeases($router);

                    for($i=0;$i<count($tmp1);$i++){
                        if($tmp1[$i]['address']==$routerData['addr']){
                            $numbers =  $tmp1[$i]['.id'];
                        }
                    }
                    unset($tmp1);
                    unset($tmp2);
                    print_r($this->RemoveDHCPLease($router,$numbers));
                    print_r($this->IpArpRemoveOne($router,$routerData['addr']));
                    break;

                case "GetAddresses":
                    $tmp = array();
                    $query_rid = new Query();
                    $query_rid->select('router_id,interface,vlan,network')->from('swconf_vlans')->where(["id"=>$post['vlan_id']]);
                    $qtmp = ($query_rid->all())[0];
                    $router = $qtmp['router_id'];
                    $interface = $qtmp['interface'];
                    $network = $qtmp['network'];
                    $vlan = $qtmp['vlan'];

                    unset($query_rid);
                    $tmp = $this->GetFullArp($router);
                    $arp = ArrayHelper::index($tmp,null,['interface'])[$interface];

                    $tmp = $this->GetAddresses($router);
                    $tmp2 = $this->GetDHCPLeases($router);

                    $leases = "<option></option>";
//                    print_r($tmp2);
                    for($j=0;$j<count($tmp2);$j++){
//                        $leases .= "<option>".json_encode($tmp2[$j])."</option>";

                        if(isset($tmp2[$j]['server'])){
                            if($tmp2[$j]['server']==$network){
//                                $leases .='<option value="addr:'.$tmp2[$j]['address'].' vlan:'.$post['vlan'].'">'.$tmp2[$j]['address'].'</option>';
                                $leases .='<option value="addr:'.$tmp2[$j]['address'].' vlan:'.$router.'">'.$tmp2[$j]['address'].'</option>';
                            }
                        }

//                        {
//".id":"*2",
//"address":"10.70.0.2",
//"mac-address":"E0:D9:E3:C2:ED:C0",
//"address-lists":"",
//"server":"10.70.0.0\/23",
//"dhcp-option":"",
//"status":"bound",
//"expires-after":"24m3s",
//"last-seen":"5m57s",
//"active-address":"10.70.0.2",
//"active-mac-address":"E0:D9:E3:C2:ED:C0",
//"active-client-id":"1:e0:d9:e3:c2:ed:c0",
//"active-server":"10.70.0.0\/23",
//"host-name":"10.70.0.2",
//"src-mac-address":"A8:F9:4B:35:A8:40",
//"radius":"false",
//"dynamic":"false",
//"blocked":"false",
//"disabled":"false"}
                    }
//                    die($leases);
                    $addrs = ArrayHelper::index($tmp,null,['interface'])[$interface][0];

                    $tmp = array();
                    $ip = explode('/', $addrs['address'])[0];
                    $mask = explode('/', $addrs['address'])[1];
                    unset($addrs);
                    $network = $this->cidr2network($ip, $mask);
                    $ip_range = $this->cidr2range($network . "/" . $mask);

                    $arp = ArrayHelper::index($arp,['address']);
                    $html = "<option></option>";
                    for($i=$ip_range[0]+2;$i<$ip_range[1];$i++){
                        if(isset($arp[long2ip($i)])){
                            if(!isset($arp[long2ip($i)]['mac-address'])){
                                $html .= "<option value='".$i."'>".long2ip($i)."</option>";
//                                $freeIP[] = long2ip($i);
                            }
                        }else{
//                            $freeIP[] = long2ip($i);
                            $html .= "<option value='".$i."'>".long2ip($i)."</option>";
                        }
                    }
                    echo json_encode(['html'=>$html,"leases"=>$leases,'tmp'=>count($tmp2)]);
                    break;

                case "GetTemplate":
                    $query_rid = new Query();
                    $query_rid->select('router_id,interface,vlan,network')->from('swconf_vlans')->where(["id"=>$post['vlan']]);

                    $tmp = ($query_rid->all())[0];
                    $router = $tmp['router_id'];
                    $vlan = $tmp['vlan'];
                    $interface = ($query_rid->all())[0]['interface'];
                    $server = $tmp['network'];
                    #$server = "sw_vlan_".$vlan;
//                    print_r($tmp);
//                    echo $router;
                    echo file_get_contents("http://10.60.248.21/swconfig.php?text=IP:".long2ip($post['address']).";".$post['mac'].";SRV:sw_vlan_".$vlan.";HN:".$post['model']);

                    $this->IpArpStaticWrite($router,$interface,long2ip($post['address']),strtoupper($post['mac']));

                    $this->DHCPlease($router,$server,long2ip($post['address']),strtoupper($post['mac']));
                    break;


            }
        }
    }

}