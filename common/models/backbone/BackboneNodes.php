<?php
namespace common\models\backbone;
use yii\db\Query;
use yii\db\Command;
use common\components\EltexSnmpAPI;
use common\components\SiteHelper;
use Yii;

/**
 * This is the model class for table "backbone_nodes".
 *
 * @property integer $id
 * @property integer $ip
 * @property string $mac
 * @property string $community
 * @property string $description
 * @property integer $ipmon_id
 * @property boolean $active
 * @property string $mount_date
 * @property string $umount_date
 */
class BackboneNodes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'backbone_nodes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {

        return [
            [['community','ip','mac','description','man_vlan'],'trim'],
            ['ip','ip','ipv4'=>true,'subnet'=> null, 'expandIPv6'=> false,"message"=>"Неверный IP адрес!"],
            [['ipmon_id','man_vlan'],'number'],
            ['mac','validateMacAddr','skipOnEmpty'=> false,'skipOnError'=>false],
            ['mount_date', 'required','message'=>'Укажите дату монтажа'],
            [['community','ip','mac','description','man_vlan','node_model'],'required', 'message'=> 'Поле обязательно' ],
            [['mount_date'], 'default', 'value' => date("Y-m-d H:i:s")],
//            ['ip','validateExistIP','skipOnEmpty'=> false,'skipOnError'=>false]

            ///Добавить валидацию при включении коммутатора и налиичии нескольких с одним IP
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
            'mac' => 'MAC адрес',
            'man_vlan' => 'VLAN управления',
            'community' => 'Community',
            'description' => 'Описание',
            'ipmon_id' => 'ID из ipmon',
            'active' => 'Действующий',
            'mount_date' => 'Замонтирован',
            'umount_date' => 'Umount Date',
            'node_model' => 'Модель',
        ];
    }

    public function validateMacAddr($attribute, $params){
        if(!filter_var($this->$attribute,FILTER_VALIDATE_MAC)){
            $this->addError($attribute, "Неверный MAC адрес!");
        }
    }

    public function validateExistIP($attribute, $params){
        $query = new Query();
        $query->select('id')->from('backbone_nodes')->where(['ip'=>ip2long($this->$attribute),'active'=>true]);
        if(count($query->all())>0){
            $this->addError($attribute, "Такой IP уже есть!".$this->$attribute);
        }
    }

    /*public static function FindNodeIP($node_id){
        $query = new Query();
        $query->select('ip')->from('backbone_nodes')->where(["active" => true,"id"=>$node_id]);
        $data = $query->all()[0];
        return $data;
    }*/

    public static function LoadList(){
        $query = new Query();
        $query->select('*')->from('backbone_nodes')->orderBy('ip');//->where(["active" => true]);
        $tmp = $query->all();
        $list = [];

        $data  = [];
        $data_disabled  = [];

        for($i = 0; $i < count($tmp); $i++){
            if($tmp[$i]['active']){
                $data[] = $tmp[$i];
            }else{
                $data_disabled[] = $tmp[$i];
            }
        }

        for ($i = 0; $i < count($data); $i++) {
            $list['nodes'][$i]['id'] = $data[$i]['id'];
            $list['nodes'][$i]['title'] = long2ip($data[$i]['ip']);
            $list['nodes'][$i]['description'] = $data[$i]['description'];
            $list['nodes'][$i]['row_class'] = '';
            $list['nodes'][$i]['mac'] = $data[$i]['mac'];;
            $list['nodes'][$i]['model'] = $data[$i]['node_model'];
            try {
                $list['nodes'][$i]['reacheable'] = true;
                $list['nodes'][$i]['icon'] = "fa fa-check-circle text-success fa-fw";
                $list['nodes'][$i]['label'] = "success";
                $list['nodes'][$i]["row_class"] = "";
                                                                                  //1.3.6.1.2.1.1.3.1
//                $list['nodes'][$i]['snmp_model']  = "1".explode(": iso",snmp2_walk($data[$i]['ip'], $data[$i]['community'], '1.3.6.1.2.1.1.2.0', 100000)[0])[1];
//                System Up Time (days,hour:min:sec):       00,20:37:30
//                $list['nodes'][$i]['uptime'] = explode(") ",snmp2_walk($data[$i]['ip'], $data[$i]['community'], '1.3.6.1.2.1.1.3.0', 100000)[0])[1];
            } catch (yii\base\ErrorException $e) {
                $list['nodes'][$i]['reacheable'] = false;
                $list['nodes'][$i]['icon'] = 'fa fa-exclamation-circle fa-fw text-danger';
                $list['nodes'][$i]['label'] = 'default';
                $list['nodes'][$i]['row_class'] = 'text-muted active';
                $tmp = $e->getMessage();
            };
        }
        unset ($data);
        return $list;
    }

    public static function MakeStatic($node,$ip,$mac,$vlan){
        $api = new EltexSnmpAPI();
        $api->MakeStatic($node,$ip,$mac,$vlan);
        //отправить INSERT в базу
    }
    public static function MakeDynamic($node,$ip,$mac,$vlan){
        $api = new EltexSnmpAPI();
        $api->RemoveStatic($node,$ip,$mac,$vlan);
        //отправить UPDATE в базу
    }

    public static function CheckNode($id){
        $data = BackboneNodes::find()->where(['id'=>$id])->asArray()->one();
        $result = [];
        $result['node_id']=$id;
        try {
            $result['reacheable'] = true;
            $result['icon'] = 'fa fa-check-circle text-success fa-fw';
            $result['snmp_model'] = "1".explode(": iso",snmp2_walk($data['ip'], $data['community'], '1.3.6.1.2.1.1.2.0', 100000)[0])[1];
            $result['uptime'] = explode(") ",snmp2_walk($data['ip'], $data['community'], '1.3.6.1.2.1.1.3.0', 100000)[0])[1];
        }catch (yii\base\ErrorException $e) {
            $result['reacheable'] = false;
            $result['snmp_model'] = '-';
            $result['uptime'] = '-';
            $result['icon'] = 'fa fa-exclamation-circle fa-fw text-danger';
        }

        return $result;
//        $list['nodes'][$i]['snmp_model']  = "1".explode(": iso",snmp2_walk($data[$i]['ip'], $data[$i]['community'], '1.3.6.1.2.1.1.2.0', 100000)[0])[1];
//                System Up Time (days,hour:min:sec):       00,20:37:30
//                $list['nodes'][$i]['uptime'] = explode(") ",snmp2_walk($data[$i]['ip'], $data[$i]['community'], '1.3.6.1.2.1.1.3.0', 100000)[0])[1];
    }

    public static function LoadARP($key){
        $arps = [];
        $hosts = [];
        //Подключаем api для Eltex
        $api = new EltexSnmpAPI();
        //Создаем экземпляр query
        $Query = new Query();

        //Запрашиваем excluded_vlan из БД для этого коммутаторы
        $Query->select('*')->from('backbone_nodes')->where("id = ".$key." and umount_date IS NULL");
        $data = ($Query->all())[0];
        $arp['excluded_vlan'] = $data['man_vlan'];

        $Query->select('*')->from('backbone_vlans')->where("backbone_node_id = ".$key);
        $db_vlans = $Query->all();
        $db_vlans_id = [];

        for($k=0;$k<count($db_vlans);$k++){
            $db_vlans_id[] = $db_vlans[$k]['id'];
        }

        //Проверка доступности коммутатора
        try {
            $tmp = snmp2_walk($data['ip'], $data['community'], '1.3.6.1.2.1.1.1.0', 100000);

            //Если доступен коммутатор, то выдергиваем с него ARP табоицу
//            $arp['data'] =
            $tmp = $api->ArpTable($data['ip'],"","");
            foreach ($tmp as $vlan => $arp_data) {
//                $arps[$vlan]['hosts'] = $arp_data[0]['ip'];
                for($i=0;$i<count($arp_data);$i++){
                    $arps[$vlan]['hosts'][ip2long($arp_data[$i]['ip'])]=['mac'=>$arp_data[$i]['mac'],'type'=>$arp_data[$i]['type']];
                }
            }

            $arp['status'] = 1;
        } catch (yii\base\ErrorException $e) {
            $tmp = $e->getMessage();
            $arp['status'] = 0;
        };

        //Запрашиваем vlanы из БД для этого коммутаторы


        $Query->select('*')->from('backbone_hosts')->where(["vlan_id"=>$db_vlans_id]);
        $db_hosts = $Query->all();

        for($k=0;$k<count($db_hosts);$k++) {
            $hosts[$db_hosts[$k]['ip']] = $db_hosts[$k];
        }

        for($k=0;$k<count($db_vlans);$k++){
            $vid = $db_vlans[$k]['vlan'];
            $arps[$vid]['static'] = 0;
            $arps[$vid]['dynamic'] = 0;

            if(isset($arps[$vid])){
                $arps[$vid] += $db_vlans[$k];
            }else{
                $arps[$vid] = $db_vlans[$k];
            }

            $tmp = SiteHelper::cidr2rangeInLong($db_vlans[$k]['network']);
            $arps[$vid]['free'] = $tmp[1]-$tmp[0]-2;
            for($t=$tmp[0]+2;$t<$tmp[1];$t++){
                if(isset($arps[$vid]['hosts'][$t])){
                    if($arps[$vid]['hosts'][$t]['type']==3){  //dynamic
                        $arps[$vid]['dynamic'] ++;
//                        $arps[$vid]['free'] --;
                    }
                    if($arps[$vid]['hosts'][$t]['type']==4){  //static
                        $arps[$vid]['static'] ++;
//                        $arps[$vid]['free'] --;
                    }

                }else{
                    $arps[$vid]['hosts'][$t] = [];
//                    $arps[$vid][''][$t] = null;
                }
                if(isset($hosts[$t])){
                    $arps[$vid]['hosts'][$t] += $hosts[$t];
                };
            }
            ksort($arps[$vid]['hosts']);
        }

        unset($db_vlans);
        unset($hosts);
        $arp['data'] = $arps;
        $arp['node'] = $key;
        return $arp;
    }
    /**
     * @inheritdoc
     * @return \common\models\query\BackboneNodesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\BackboneNodesQuery(get_called_class());
    }

    public static function InsertNode($post){
        if($post['active']){
            $active = 'true';
        }else{
            $active = 'false';
        }

        if($post['ipmon_id']==''){
            $post['ipmon_id'] = 0;
        }

        Yii::$app->db->createCommand("INSERT INTO backbone_nodes (ip,mac,description,community,mount_date,ipmon_id,active,man_vlan) VALUES (".ip2long($post['ip']).",'".$post['mac']."','".$post['description']."','".$post['community']."','".date('Y-m-d H:i:s')."',".$post['ipmon_id'].",".$active.",".$post['man_vlan'].")")->queryAll();
        return($post);
    }
}
