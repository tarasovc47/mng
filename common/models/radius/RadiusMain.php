<?php
namespace common\models\radius;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\data\SqlDataProvider;
/**
 * Created by PhpStorm.
 * User: asmild
 * Date: 12.12.17
 * Time: 11:51
 */
class RadiusMain  extends \yii\db\ActiveRecord
{
    public $login;
    public $macaddr;
    public $ipv4;
    public $ipv6;
    public $nas;
    public $status;
    public $begin;
    public $end;

    public function attributeLabels()
    {
        return [
            'login' => 'Логин',
            'macaddr' => 'MAC адрес',
            'ipv4' => 'IP адрес',
            'nas' => 'NAS',
            'ipv6' => 'IPv6 префикс',
            'status' => 'Статус',
            'begin' => 'От',
            'end' => 'До',

        ];
    }

    public function rules()
    {
        return [

            [['login','macaddr','ipv4','ipv6'],'trim'],
            ['ipv4','ip','ipv4'=>true,'subnet'=> null, 'expandIPv6'=> false,"message"=>"Неверный IP адрес!"],
            ['macaddr','validateMacAddr','skipOnEmpty'=> true,'skipOnError'=>true],
            [['begin','end'],'required'],
            [['login','macaddr','ipv4','ipv6'],function($attribute, $params, $validator){
                if(empty($this->login) && empty($this->macaddr) && empty($this->ipv4) && empty($this->ipv6)) {
                    $this->addError($attribute,"Заполните хотя бы одно из полей");
                }
            },'skipOnEmpty'=> false,'skipOnError'=>false],
        ];
    }

    public function validateMacAddr($attribute, $params){
        if(!filter_var($this->$attribute,FILTER_VALIDATE_MAC)){
            $this->addError($attribute, "Неверный MAC адрес!");
        }
    }



    public static function loadNAS(){
        $db_radius = pg_connect("
            host=10.60.249.8
            dbname=radiusmain
            user=web
            password=webparol
            connect_timeout=1");
        $nas=[];
        $query = pg_query($db_radius,"SELECT n.id,n.ipaddress,nt.name,nt.descript FROM nas n JOIN nas_type nt ON n.nastype=nt.id ORDER by n.id");
        while ($data = pg_fetch_assoc($query)) {
            $nas[$data['id']] = $data;
        }
        pg_close($db_radius);
        return $nas;
    }

    private $pg_username = 'web';
    private $pg_userpass = 'webparol';
    private $RadiusServers = [    ///список радиус серверов, - перенести в БД с создание странички редактирования этого списка.
        "10.60.249.8" => "radiusmain",
//                                     //временно недоступен
//            "10.60.249.9" => "radiusmain",
//
        "10.60.249.11" => "radius",
        "10.60.249.12" => "radius",
        "10.60.249.13" => "radius"
    ];

    public function Disconnect($login,$session_name){
        $ip_main = "10.60.249.8";
        $query = new RadiusMain;
        $sess_data = [];
        $tmp = ($query->Accounting($login, null, null, null))[$ip_main]['data'];
        foreach ($tmp as $sessnum => $accounting){
            if($accounting['session_name']==$session_name){
                $sess_data = $accounting;
            }
        }
//                $sess_data = ($query->Accounting($login, null, null, null))[$ip_main]['data'];
//                SiteHelper::debug($sess_data);
//                die();
        $ipv4 = $sess_data['ipv4_addr'];
//        $data['ipv4'] = $ipv4;
        try {
            $db_connections[$ip_main] = pg_connect("
            host=" . $ip_main . "
            dbname=" . $query->RadiusServers[$ip_main] . "
            user=$this->pg_username
            password=$this->pg_userpass
            connect_timeout=1");
            $query = pg_query($db_connections[$ip_main], "SELECT * FROM subscriber WHERE login = '" . $login . "'");
            $subcriber_data = pg_fetch_assoc($query);
            $data[$ip_main]['trusted'] = false;
            if (pg_num_rows($query) > 0) {
                $data[$ip_main]['trusted'] = true;
            }
            unset($query);
            if ($data[$ip_main]['trusted']) {
                $query = pg_query($db_connections[$ip_main], "SELECT * FROM sess_disconnect WHERE user_name = '" . $login . "' and session_name = '" . $session_name . "'");
                if (pg_num_rows($query) == 0) {
                    $query_insert = pg_query($db_connections[$ip_main], "INSERT INTO sess_disconnect (session_name,user_name,ipv4_addr) VALUES ('" . $session_name . "','" . $login . "','" . $ipv4 . "') RETURNING ts,session_name;");
                    $data[$ip_main]['task'] = pg_fetch_assoc($query_insert);
                    $data[$ip_main]['new_task'] = true;
                } else {
                    $data[$ip_main]['task'] = pg_fetch_assoc($query);
                    $data[$ip_main]['new_task'] = false;
                }
            }
//                $query=pg_query($db_connections[$ip],"SELECT * FROM accounting WHERE login = '".$supplied_login."' ORDER by started_at DESC LIMIT 10");

            /*
             * (
              ts timestamp without time zone DEFAULT (now())::timestamp without time zone,
              session_name character varying NOT NULL,
              user_name character varying NOT NULL,
              ipv4_addr inet NOT NULL,
              result_code character varying,
              result_ts timestamp without time zone
            )*/


            $data[$ip_main]['server'] = $ip_main;
            $data[$ip_main]['connection_status'] = 1;
            $data[$ip_main]['connection_status_msg'] = "OK";
            pg_close($db_connections[$ip_main]);
        } catch (yii\base\ErrorException $e) {
            $data[$ip_main]['server'] = $ip_main;
            $data[$ip_main]['connection_status'] = 0;
            $data[$ip_main]['connection_status_msg'] = $e->getMessage();

            foreach ($this->RadiusServers as $ip => $dbname) {
                if ($ip != $ip_main) {
                    try {
                        $db_connections[$ip] = pg_connect("
            host=" . $ip . "
            dbname=" . $dbname . "
            user=$this->pg_username
            password=$this->pg_userpass
            connect_timeout=1");
//                $query=pg_query($db_connections[$ip],"SELECT * FROM accounting WHERE login = '".$supplied_login."' ORDER by started_at DESC LIMIT 10");
                        $query = pg_query($db_connections[$ip_main], "SELECT * FROM subscriber WHERE login = '" . $login . "'");
                        $subcriber_data = pg_fetch_assoc($query);
                        $data[$ip]['trusted'] = false;
                        if (pg_num_rows($query) > 0) {
                            $data[$ip]['trusted'] = true;
                        }
                        unset($query);
                        if ($data[$ip]['trusted']) {
                            $query = pg_query($db_connections[$ip], "SELECT * FROM sess_disconnect WHERE user_name = '" . $login . "' and session_name = '" . $session_name . "' and result_code IS NULL");
                            if (pg_num_rows($query) == 0) {
                                $query_insert = pg_query($db_connections[$ip], "INSERT INTO sess_disconnect (session_name,user_name,ipv4_addr) VALUES ('" . $session_name . "','" . $login . "','" . $ipv4 . "') RETURNING ts,session_name;");
                                $data[$ip]['task'] = pg_fetch_assoc($query_insert);
                                $data[$ip]['new_task'] = true;
                            } else {
                                $data[$ip]['task'] = pg_fetch_assoc($query);
                                $data[$ip]['new_task'] = false;
                            }


                        }

                        $data[$ip]['server'] = $ip;
                        $data[$ip]['connection_status'] = 1;
                        $data[$ip]['connection_status_msg'] = "OK";
                        pg_close($db_connections[$ip]);
                    } catch (yii\base\ErrorException $e) {
                        $data[$ip]['server'] = $ip;
                        $data[$ip]['connection_status'] = 0;
                        $data[$ip]['connection_status_msg'] = $e->getMessage();
//                echo $e->getMessage();
                    }
                }

            }
        }
        return $data;
    }


    public function Accounting($login,$macaddr,$ipv4,$ipv6){
        $db_connections = [];
        $data = [];
        $tmp = [];
        $total_num = 0;

        foreach ($this->RadiusServers as $ip=>$dbname) {
            try {
                $db_connections[$ip] = pg_connect("
            host=" . $ip . "
            dbname=" . $dbname . "
            user=$this->pg_username
            password=$this->pg_userpass
            connect_timeout=1");
//                $query=pg_query($db_connections[$ip],"SELECT * FROM accounting WHERE login = '".$supplied_login."' ORDER by started_at DESC LIMIT 10");
                $query=pg_query($db_connections[$ip],"  SELECT session_name, login, nas_ipaddr, nas_port, started_at, updated_at, session_uptime, downstream_octets, 
            upstream_octets, calling_station, ipv4_addr, ipv6_prefix, circuit_id, active_svcs, 
            (
            select to_json( array_agg( row( service_name, command_codes ) ) )  from
            (
            select service_name, array_agg(command_code) from service_auth sa where sa.session_name = acc.session_name and 
               ts in (select ts from service_auth sa where sa.session_name = acc.session_name group by 1 order by 1 desc limit 2 )
            group by 1
            ) as a( service_name, command_codes )
            ) as svcs
        FROM accounting acc
        WHERE stopped_at IS NULL and (acc.login = '".$login."' or acc.calling_station = '".$macaddr."' or acc.ipv4_addr = '".$ipv4."' or acc.ipv6_prefix = '".$ipv6."')
        ORDER BY started_at DESC");

                $num = pg_num_rows($query);
                $total_num = $total_num + $num;

                $data[$ip]['accounting_nums'] = $num;

                if($num>0) {     //ищем не пустой результат
//            if(empty($data)){     //ищем не пустой результат
                    while($accdata = pg_fetch_assoc($query)){
                        $data[$ip]['data'][] = $accdata;
                    }
                }else{

                }

                $data[$ip]['server'] = $ip;
                $data[$ip]['connection_status'] = 1;
                $data[$ip]['connection_status_msg'] = "OK";
                pg_close($db_connections[$ip]);
            }catch (yii\base\ErrorException $e){
                $data[$ip]['server'] = $ip;
                $data[$ip]['connection_status'] = 0;
                $data[$ip]['connection_status_msg'] = $e->getMessage();
//                echo $e->getMessage();
            }
        }
        return $data;
    }
}