<?php
namespace common\models\backbone;
use yii\db\Query;
use yii\db\Command;
use common\components\EltexSnmpAPI;
use common\components\SiteHelper;
use Yii;
use common\models\backbone\BackboneVlans;

/**
 * This is the model class for table "backbone_hosts".
 *
 * @property integer $id
 * @property integer $vlan_id
 * @property integer $ipmon_id
 * @property integer $ip
 * @property string $description
 * @property string $mac
 * @property integer $sw_model
 * @property boolean $active
 * @property string $mount_date
 * @property string $umount_date
 */

class BackboneHosts extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'backbone_hosts';
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
            'vlan' => 'VLAN',
            'description' => 'Описание',
            'ipmon_id' => 'ID из ipmon',
            'active' => 'Статический IP',
            'mount_date' => 'Замонтирован',
            'umount_date' => 'Umount Date',
            'sw_model' => 'Модель',
            'configured' => 'Сконфигурировать',
        ];
    }

    /**
     * @inheritdoc
     */


    public function rules()
    {

        return [
            [['ip','mac','description'],'trim'],
            ['ip','ip','ipv4'=>true,'subnet'=> null, 'expandIPv6'=> false,"message"=>"Неверный IP адрес!"],
            [['ipmon_id'],'number'],
            ['mac','checkMAC','on'=>'create','skipOnEmpty'=> false,'skipOnError'=>false],
//            ['mac','unique',  'message' => 'Мак адрес уже привязан '.$this->LoadHOstByMac('{value}'),'on'=>'create'],
            ['mac','validateMacAddr','skipOnEmpty'=> false,'skipOnError'=>false],
//            ['mount_date', 'required','message'=>'Укажите дату монтажа'],
            [['ip','mac','description'],'required', 'message'=> 'Поле {attribute} обязательно' ],
            [['mount_date'], 'default', 'value' => date("Y-m-d H:i:s")],
//            ['ip','validateExistIP','skipOnEmpty'=> false,'skipOnError'=>false]

            ///Добавить валидацию при включении коммутатора и налиичии нескольких с одним IP
        ];
    }

    public function checkMAC($attribute, $params){
        if(BackboneHosts::find()->where(['mac' => strtoupper($this->$attribute)])->exists()){
            $row = BackboneHosts::find()
                ->select('backbone_hosts.ip as hostip,vlan_id,backbone_nodes.ip as boneip,backbone_nodes.description as bonedesc,backbone_node_id')
                ->where(['backbone_hosts.mac' => strtoupper($this->$attribute)])
                ->leftJoin('backbone_vlans','backbone_vlans.id=backbone_hosts.vlan_id')
                ->leftJoin('backbone_nodes','backbone_nodes.id=backbone_vlans.backbone_node_id')
                ->asArray()
                ->all();
            $row=$row[0];

            ///Сделать кнопку "удалить привязку"
            $this->addError($attribute, 'МAC уже привязан к '.long2ip($row['hostip']).' на <a target="_blank" href="/ipmon/backbone/node/'.$row['backbone_node_id'].'">'.$row['bonedesc'].' ['.long2ip($row['boneip']).' ]</a>');
        };
    }

    private function LoadHOstByMac($val){
        $macQuery = new Query();
        $mac['val'] = trim($val);
        $macQuery->select('*')
            ->from('backbone_hosts')
            ->where("mac = '".trim($val)."'");
//
        $command = $macQuery->createCommand();
        $mac['comm'] = $command;
        $mac['dat'] = $command->queryAll();
        return json_encode($mac);
//        return $mac;
    }


    public function validateMacAddr($attribute, $params){
        if(!filter_var($this->$attribute,FILTER_VALIDATE_MAC)){
            $this->addError($attribute, "Неверный MAC адрес!");
        }
    }

    public function LoadHost($hostData){
        $Query = new Query();
//        $query->select('id')->from('backbone_nodes')->where(['ip'=>ip2long($this->$attribute),'active'=>true]);
        $api = new EltexSnmpAPI();
        $tmp = $api->MacByIP(long2ip($hostData['nodeIP']),$hostData['vlan'],$hostData['hostIP']);
        $data = [];
        $Query->select('*')->from('backbone_hosts')->where(["vlan_id"=>$hostData['vlanID'],'ip'=>$hostData['hostIP']]);
        $data = $Query->all();
        if(count($data)>0){
            $data = $data[0];
        }
        if(isset($tmp[0]['mac'])){
            $data['ip'] =ip2long($tmp[0]['ip']);
            $data['mac'] = strtoupper($tmp[0]['mac']);
            $data['active'] = $tmp[0]['type']-3;
            if(!isset($data['description'])){
                $data['description'] = '';
            };
        }else{

        }
        return $data;
    }

    public function LoadModels(){
        $Query = new Query();
        $Query->select('*')->from('backbone_models');
        $data = $Query->all();
        $list = [];
        for($i=0;$i<count($data);$i++){
            $list[$data[$i]['id']] = $data[$i]['vendor'].' '.$data[$i]['model'];
        }
        return $list;
    }

    public function LoadModelsByOIDs(){
        $Query = new Query();
        $Query->select('*')->from('backbone_models');
        $data = $Query->all();
        $list = [];
        for($i=0;$i<count($data);$i++){
            if(isset($data[$i]['snmp'])){
                $list[$data[$i]['snmp']] = $data[$i]['vendor'].' '.$data[$i]['model'];
            }
        }
        return $list;
    }

    public static function LoadModelsSnmp(){
        $Query = new Query();
        $Query->select('*')->from('backbone_models');
        $data = $Query->all();
        $list = [];
        for($i=0;$i<count($data);$i++){
            if(isset($data[$i]['snmp'])){
                $list[$data[$i]['snmp']] = $data[$i];
            }
        }
        return $list;
    }
}