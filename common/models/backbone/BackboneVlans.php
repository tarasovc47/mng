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
 * @property integer $vlan
 * @property string $backbone_node_id
 * @property string $description
 * @property integer $network
 * @property boolean $active
 * @property string $create_date
 * @property string $destroy_date
 */

class BackboneVlans extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'backbone_vlans';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'vlan' => 'VLAN',
            'network' => 'Подсеть',
            'description' => 'Описание',
            'active' => 'Действующий',
            'create_date' => 'Дата создания',
            'destroy_date' => 'Разобран',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {

        return [
            [['vlan','backbone_node_id'],'required'],
            ['vlan','number','max'=>3999],
            ['create_date','required','message'=>'Укажите дату создания'],
            ['network','required','message'=>'Укажите подсеть'],
            ['network','validateNetwork','skipOnEmpty'=> false,'skipOnError'=>false],
//            ['mask','number','message'=>'Укажите дату создания'],
//            [['mask'],'number','min'=>1,'max'=>32],
        ];
    }

    public static function LoadVlan($node,$vlan){
        $query = new Query();
        $query
            ->select('*')
            ->from('backbone_vlans')
            ->where("backbone_node_id = ".$node." and id = ".$vlan." ORDER by create_date LIMIT 1");
        return $query->all();
    }

    public static function CreateVlan($data){
        $query = new Query();
        if($data['active']){
            $active = 'true';
        }else{
            $active = 'false';
        }

        return 1;
    }

    public function validateNetwork($attribute, $params){
        $is_network = false;
        $network = $this->$attribute;
        if(strpos($network,'/')){
            $tmp = explode("/",$network);
            $lastOctet = explode(".",$tmp[0])[3];
            $validSubnet = (filter_var($tmp[0],FILTER_VALIDATE_IP))&&($lastOctet==0);
            $validMask = (is_numeric($tmp[1]))&&($tmp[1]<=32);
            if($validSubnet&&$validMask){
                $is_network = true;
            }
        }
        if(!$is_network){
            $this->addError($attribute, "Ошибка в описании подсети!");
        }
    }

    public static function find()
    {
        return new \common\models\query\BackboneNodesQuery(get_called_class());
    }


}