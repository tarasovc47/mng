<?php

namespace common\models\arps;
use common\components\RouterosAPI;
use common\components\SiteHelper;
use yii\db\Query;

class ArpTables extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'routers';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {

        return [
            [['user','ip','apipass','rwuser','apiwrpass'],'trim'],
            ['ip','ip','ipv4'=>true,'subnet'=> null, 'expandIPv6'=> false,"message"=>"Неверный IP адрес!"],
            [['ip','user','apipass','apiwrpass','rwuser','description'], 'required',"message"=>'Обязательные поля'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ip' => 'IP адрес',
            'description' => 'Описание',
            'user' => 'Пользователь read',
            'apipass' => 'Пароль read',
            'rwuser' => 'Пользователь write',
            'apiwrpass' => "Пароль write",
            'visible' => 'Видимость',
            'text' => 'Описание назначения',
        ];
    }

    public $EmptyMAC = '-';

    public static function LoadAccesses($user,$dep,$router){
        $query = new Query();
        $query->select('*')->from('network_access')->where("(user_id = ".$user." OR department_id = ".$dep.") AND router_id = ".$router);
        $tmp = $query->all();
        return $tmp;
    }

    public static function LoadCredentials($router){
        $query = new Query();
        $query->select('*')->orderBy('ip')->where(["ip" => long2ip($router)]);
        $tmp = $query->all();
        return $tmp;
    }

    public static function IPAddressesPrint($credentials){
        $ip = $credentials->ip;
        $user = $credentials->user;
        $pass = $credentials->apipass;
        $API = new RouterosAPI();
//        $API->debug = true;
        $ARRAY = [];
        if ($API->connect($ip, $user, $pass)) {
            $ARRAY = $API->comm("/ip/address/print");
//            $ARRAY = $API->read();
            $API->disconnect();
            $ARRAY['timeout'] = 0;
        }else{
            $ARRAY['timeout'] = 1;
        }
        return $ARRAY;
    }


    public static function IpArpFindOne($credentials,$addr){
        $ip = $credentials->ip;
        $user = $credentials->user;
        $pass = $credentials->apipass;
//        echo $addr;
        $API = new RouterosAPI();
//        $API->debug = true;
        $ARRAY = array();
        if ($API->connect($ip, $user, $pass)) {
//        if ($API->connect('217.116.48.94', 'apiuser', 'r8eBhfLKaR')) {
            $ARRAY = $API->comm("/ip/arp/print",array(
                "?address" => $addr
            ));
//            print_r($ARRAY);
//            $READ = $API->read();
//            $ARRAY = $API->parse_response($READ);
            $API->disconnect();
        }
        return $ARRAY;
    }

    public static function IpArpRemoveOne($credentials,$addr){
        $ip = $credentials->ip;
        $user = $credentials->rwuser;
        $pass = $credentials->apiwrpass;
        $API = new RouterosAPI();
//        $API->debug = true;
        $ARRAY = array();
        if ($API->connect($ip, $user, $pass)) {

            $ARRAY = $API->comm("/ip/arp/remove",[
                "numbers" =>$API->comm("/ip/arp/print",[
                    "?address" => $addr
                ])[0]['.id']
            ]);

            /*$API->comm("/ip/firewall/address-list/remove", array (
"numbers" => "[find list=zapret]" ,))*/
            $API->disconnect();
        }
        return $ARRAY;

    }

    public static function IpArpPrint($credentials){
        $ip = $credentials->ip;
        $user = $credentials->user;
        $pass = $credentials->apipass;
        $API = new RouterosAPI();
//        $API->debug = true;
        $ARRAY = array();
//        $propertice = array(
//            ".proplist"=> .id,name",
//        );
        if ($API->connect($ip, $user, $pass)) {
//        if ($API->connect('217.116.48.94', 'apiuser', 'r8eBhfLKaR')) {
            $API->write("/ip/arp/print",[ ".proplist"=> "address,mac-address,interface"]);
            $ARRAY = $API->read();
            $API->disconnect();
        }
        return $ARRAY;
    }

    private function InterfaceGetall($credentials){
        $ip = $credentials->ip;
        $user = $credentials->user;
        $pass = $credentials->apipass;
        $API = new RouterosAPI();
//        $API->debug = true;
        $ARRAY = array();
//        $propertice = array(
//            ".proplist"=> .id,name",
//        );
        if ($API->connect($ip, $user, $pass)) {
//        if ($API->connect('217.116.48.94', 'apiuser', 'r8eBhfLKaR')) {
            $API->write("/interface/print");
            $ARRAY = $API->read();
            $API->disconnect();
        }
        return $ARRAY;
    }

    private function InterfaceVlanPrint($credentials){
        $ip = $credentials->ip;
        $user = $credentials->user;
        $pass = $credentials->apipass;
        $API = new RouterosAPI();
        $ARRAY = array();
        if ($API->connect($ip, $user, $pass)) {
//        if ($API->connect('217.116.48.94', 'apiuser', 'r8eBhfLKaR')) {
            $ARRAY = $API->comm('/interface/vlan/print');
            $API->disconnect();
        }
        return $ARRAY;
    }

    public function IpArpPrintInterface($int,$credentials){
        $ip = $credentials->ip;
        $user = $credentials->user;
        $pass = $credentials->apipass;
        $API = new RouterosAPI();
        $ARRAY = array();
        if ($API->connect($ip, $user, $pass)) {
            $ARRAY = $API->comm('/ip/arp/print', ["?interface" => $int]);
            $API->disconnect();
        }
        return $ARRAY;
    }

    public static function IpArpPrintIP($credentials,$addr){
        $ip = $credentials->ip;
        $user = $credentials->user;
        $pass = $credentials->apipass;

        $API = new RouterosAPI();
        $ARRAY = array();
        if ($API->connect($ip, $user, $pass)) {
//        if ($API->connect('217.116.48.94', 'apiuser', 'r8eBhfLKaR')) {
            $ARRAY = $API->comm('/ip/arp/print', ["?address" => $addr]);
            $API->disconnect();
        }
        return $ARRAY;
    }

    public static function IpArpAddComment($credentials,$addr,$comment){
        $ip = $credentials->ip;
        $user = $credentials->rwuser;
        $pass = $credentials->apiwrpass;
//        $comment = iconv('utf-8', 'cp1251', $comment);
        $comment =  mb_convert_encoding($comment, 'cp1251', mb_detect_encoding($comment));
        $ARRAY = array();
        $API_wr = new RouterosAPI();
        if ($API_wr->connect($ip, $user, $pass)) {
            $ARRAY = $API_wr->comm("/ip/arp/set", array(
                "comment" => $comment,
                "numbers" => $API_wr->comm("/ip/arp/print",array(
                    "?address" => $addr
                ))[0]['.id']
            ));
            $API_wr->disconnect();
        }
        return $ARRAY;
    }

    public static function IpArpStaticWrite($credentials,$interface,$addr,$mac){
        $ip = $credentials->ip;
        $user = $credentials->rwuser;
        $pass = $credentials->apiwrpass;
        $ARRAY = array();
        $API_wr = new RouterosAPI();

        if ($API_wr->connect($ip, $user, $pass)) {
            $ARRAY = $API_wr->comm("/ip/arp/add", [
                "mac-address" => $mac,
                "address" => $addr,
                "interface" => $interface
            ]);
            $API_wr->disconnect();
        }
        if ($API_wr->connect($ip, $user, $pass)) {
            $ARRAY = $API_wr->comm("/ip/dhcp-server/lease/make-static", [
                "address" => $addr,
            ]);
            $API_wr->disconnect();
        }
        return $ARRAY;
    }

    public static function DhcpStaticLeaseAdd($credentials,$server,$addr,$mac){
        $ip = $credentials->ip;
        $user = $credentials->rwuser;
        $pass = $credentials->apiwrpass;
        $ARRAY = array();
        $API_wr = new RouterosAPI();


        if ($API_wr->connect($ip, $user, $pass)) {
            $ARRAY = $API_wr->comm("/ip/dhcp-server/lease/add", [
                "mac-address" => $mac,
                "address" => $addr,
                "server" => $server
            ]);
            $API_wr->disconnect();
        }
        return $ARRAY;
    }

    public static function DhcpStaticLeaseLoad($credentials){
        $ip = $credentials->ip;
        $user = $credentials->user;
        $pass = $credentials->apipass;
        $ARRAY = array();
        $API = new RouterosAPI();
        if ($API->connect($ip, $user, $pass)) {
            $API->write("/ip/dhcp-server/lease/print");
//            $API->write("interface",$int);
            $ARRAY = $API->read();
            $API->disconnect();
        }
        return $ARRAY;
    }

    public static function DhcpStaticLeaseRemove($credentials,$server,$addr,$mac){
        $ip = $credentials->ip;
        $user = $credentials->rwuser;
        $pass = $credentials->apiwrpass;
        $ARRAY = array();
        $API_wr = new RouterosAPI();
        $tmp1 = ArpTables::DhcpStaticLeaseLoad($credentials);
        for($i=0;$i<count($tmp1);$i++){
            if($tmp1[$i]['address']==$addr){
                $numbers =  $tmp1[$i]['.id'];
            }
        }

        if ($API_wr->connect($ip, $user, $pass)) {
            $ARRAY = $API_wr->comm("/ip/dhcp-server/lease/remove",[
                "numbers" => $numbers
            ]);
            $API_wr->disconnect();
        }
        return $ARRAY;
    }

    public function Networks ($credentials)
    {
        $vlans = $this->InterfaceVlanPrint($credentials);
        $addresses = $this->IPAddressesPrint($credentials);
//        SiteHelper::debug($vlans);
//        SiteHelper::debug($addresses);
//        die();
        /*
        $arp = $this->IpArpPrint($credentials);
//        $interfaces = $this->InterfaceGetall();
        $tabs = [];
        $tabs['timeout'] = 0;
        if(!$addresses['timeout']){
            unset($addresses['timeout']);
            for ($i = 0; $i < count($addresses); $i++) {
                $ip = explode('/', $addresses[$i]['address'])[0];    //Получаем IP подсети
                $mask = explode('/', $addresses[$i]['address'])[1];     //Получаем маску подсети
                $network = $this->cidr2network($ip, $mask);                     //Получаем подсеть
                $ip_range = $this->cidr2range($network . "/" . $mask);      //Получаем границы подсети
                if(($addresses[$i]['interface']!='backbone_control')&&
                    ($addresses[$i]['interface']!='br_control')&&
                    ($addresses[$i]['interface']!='backbone_up')
                ) {
                    if ($mask != 32) {   //Если маска не 32, т.е. не одиночный адрес
                        $tabs[$i] = array(
                            'address' => $addresses[$i]['address'],
                            'interface' => $addresses[$i]['interface'],
                            'mask' => $this->cidr2netmask($mask),
                            'cidr' => $mask,
                            'broadcast' => ip2long($ip_range[1]),
                            'range' => $ip_range,
                            'id' => "networkId-" . $i
                        );

                        if (isset($addresses[$i]['comment'])) {  //Если есть комментарии, то добавляем в массив
//                    $tabs[$i]['comment'] = mb_convert_encoding($addresses[$i]['comment'], 'utf-8', mb_detect_encoding($addresses[$i]['comment']));
                            $tabs[$i]['comment'] = iconv('cp1251', 'utf-8', $addresses[$i]['comment']);
                        }
                        $tabs[$i]['vid'] = -$i; //Задаем Vlan ID при
                        for ($j = 0; $j < count($vlans); $j++) {
                            if ($vlans[$j]['name'] == $addresses[$i]['interface']) {
                                if (!isset($vlans[$j]['comment'])) {
                                    $vlans[$j]['comment'] = "";
                                }
                                $tabs[$i]['vlan'] = array(
                                    'vlan_id' => $vlans[$j]['vlan-id'],
                                    'comment' => mb_convert_encoding($vlans[$j]['comment'], 'utf-8', 'cp1251'),
//                            'comment' => iconv('cp1251', 'utf-8', $vlans[$j]['comment']),
                                    'name' => $vlans[$j]['name'],
                                );
                                $tabs[$i]['vid'] = $vlans[$j]['vlan-id'];
                            }

                        }
                        $tabs[$i]['FreeCount'] = 0;
                        $tabs[$i]['StaticCount'] = 0;
                        $tabs[$i]['DynamicCount'] = 0;


                        if (1) {
                            for ($j = 1; $j < (ip2long($ip_range[1]) - ip2long($ip_range[0])); $j++) {
                                $tabs[$i]['FreeCount']++;
                            }
                            for ($j = 0; $j < count($arp); $j++) {
                                if ($tabs[$i]['interface'] == $arp[$j]['interface']) {
                                    $tabs[$i]['arp'][] = $arp[$j]['address'];
                                    if (isset($arp[$j]['address'])) {
                                        $tabs[$i]['FreeCount']--;
                                    }
                                    if ($arp[$j]['dynamic'] == 'false') {
                                        $tabs[$i]['StaticCount']++;
                                    } else {
                                        $tabs[$i]['DynamicCount']++;
                                        if (isset($arp[$j]['address'])) {
                                            if ($arp[$j]['address'] == '00:00:00:00:00:00') {

                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }else{
            $tabs['timeout'] = 1;
        }
//        return ($addresses);
        */
        $tabs = [
            'vlans'=>$vlans,
            'addrs'=>$addresses
        ];
        return $tabs;
    }

    public function ARP ($net,$credentials)
    {
//        print_r($net);
        $vlans = $this->InterfaceVlanPrint($credentials);
        $addresses = $this->IPAddressesPrint($credentials);
        $arp = $this->IpArpPrintInterface($net,$credentials);
        $interfaces = $this->InterfaceGetall($credentials);
//                    $jsonData = array();
//                    $jsonData['arp'] = $arp;
//                    $jsonData['vlan'] = $VlansList;
//                    $jsonData['address'] = $AddressesList;

        $tabs = array();

//        return SiteHelper::debug($arp);


        if(!$addresses['timeout']){
            unset($addresses['timeout']);
            for ($i = 0; $i < count($addresses); $i++) {
                if ($addresses[$i]['interface'] == $net) {
                    $ip = explode('/', $addresses[$i]['address'])[0];
                    $mask = explode('/', $addresses[$i]['address'])[1];
                    $network = $this->cidr2network($ip, $mask);;
                    $ip_range = $this->cidr2range($network . "/" . $mask);


                    if ($mask != 32) {
                        $tabs[$i] = array(
                            'address' => $addresses[$i]['address'],
                            'interface' => $addresses[$i]['interface'],
                            'mask' => $this->cidr2netmask($mask),
                            'broadcast' => $ip_range[1],
                            'range' => $ip_range,
                            'gateway' => $ip
                        );

                        for ($j = 0; $j < count($vlans); $j++) {
                            if ($vlans[$j]['name'] == $addresses[$i]['interface']) {
                                if (!isset($vlans[$j]['comment'])) {
                                    $vlans[$j]['comment'] = "";
                                }
                                $tabs[$i]['vlan'] = array(
                                    'vlan' => $vlans[$j]['vlan-id'],
//                                    'comment' => mb_convert_encoding($vlans[$j]['comment'], 'utf-8', mb_detect_encoding($vlans[$j]['comment'])),
                                    'comment' => iconv('cp1251', 'utf-8', $vlans[$j]['comment']),
                                    'name' => $vlans[$j]['name'],
                                );
                            }
                        }
                        $tabs[$i]['freeCount'] = 0;
                        $tabs[$i]['staticCount'] = 0;
                        $tabs[$i]['totalCount'] = ip2long($ip_range[1])-ip2long($ip_range[0])-1;

                        ///Счетчики надо проверять!!
                        if (1) {
                            for ($j = 1; $j < (ip2long($ip_range[1]) - ip2long($ip_range[0])); $j++) {
                                if (long2ip(ip2long($ip_range[0]) + $j) == $ip) {
                                    $mac = '';
                                    for ($k = 0; $k < count($interfaces); $k++) {
                                        if ($interfaces[$k]['name'] == $addresses[$i]['interface']) {
                                            $mac = $interfaces[$k]['mac-address'];
                                        }
                                    }
                                    $tabs[$i]['hosts'][long2ip(ip2long($ip_range[0]) + $j)] = array(
                                        'free' => 'false',
                                        'MAC' => $mac,
                                        'dynamic' => 'false',  //Для адресов что не в АРП
                                        'disabled' => 'false', //Для адресов что не в АРП
                                        'comment' => '',
                                        'fixed' => true
                                    );
                                } else {
                                    $tabs[$i]['hosts'][long2ip(ip2long($ip_range[0]) + $j)] = array(
                                        'free' => 'true',
                                        'MAC' => $this->EmptyMAC,
                                        'dynamic' => 'true',  //Для адресов что не в АРП
                                        'disabled' => 'false', //Для адресов что не в АРП
                                        'comment' => '',
                                        'fixed' => false
                                    );
                                }
                                $tabs[$i]['freeCount']++;
                            }

                            for ($j = 0; $j < count($arp); $j++) {
                                if (isset($arp[$j]['mac-address'])) {
                                    if (isset($tabs[$i]['hosts'][$arp[$j]['address']])) {
                                        $tabs[$i]['hosts'][$arp[$j]['address']] = array(
                                            'free' => 'false',
                                            'MAC' => $arp[$j]['mac-address'],
                                            'dynamic' => $arp[$j]['dynamic'],
                                            'disabled' => $arp[$j]['disabled'],
                                            'comment' => '',
                                            'fixed' => false
                                        );
                                        $tabs[$i]['freeCount']--;
                                        if ($arp[$j]['dynamic'] == 'false') $tabs[$i]['staticCount']++;
                                        if (isset($arp[$j]['comment'])) {
                                            $tabs[$i]['hosts'][$arp[$j]['address']]['comment'] = iconv('cp1251', 'utf-8', $arp[$j]['comment']);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $tabs;
    }

    public function cidr2network($ip, $cidr)
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
        return [long2ip($range_start), long2ip($range_end)];
    }


    public static function find()
    {
        return new \common\models\query\ArpTablesQuery(get_called_class());
    }

    public static function Saverouter($data){
        if (ArpTables::find()->where(['ip' => $data['ip']])->exists()) {
                 $model = ArpTables::findOne(['ip' => $data['ip']]);
//         if(!$data['active']){
//             $model->destroy_date = date('Y-m-d H:i:s');
//         }else{
//             $model->destroy_date = null;
//         }
             } else {
                 $model = new ArpTables();
//         $model->create_date = date('Y-m-d H:i:s');
             }
             $model->ip = $data['ip'];
             $model->description = $data['description'];
             $model->visible = $data['visible'];
             $model->user = $data['user'];
             $model->apipass = $data['apipass'];
             $model->rwuser = $data['rwuser'];
             $model->text = $data['text'];
             $model->apiwrpass = $data['apiwrpass'];
           if ($model->save(false)) {
                 $ans = 'Ok';
             } else {
                 $ans = 'Err';
             }
        return $ans;
    }
}