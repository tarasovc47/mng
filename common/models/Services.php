<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\db\Query;

class Services extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'services';
    }

    public function rules()
    {
        return [
            [['name', 'machine_name', 'billing_id', 'global_service_id'], 'required'],
            [['name', 'machine_name'], 'string', 'max' => 255],
            [['name', 'machine_name'], 'trim'],
            [['billing_id', 'global_service_id'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'machine_name' => 'Машинное название',
            'global_service_id' => 'Услуга',
            'billing_id ' => 'ID в биллинге',
            'status' => 'Статус',
        ];
    }

    public function getConnectionTechnologies(){
        return $this->hasMany(ConnectionTechnologies::className(), ['service_id' => 'id']);
    }

    public static function find()
    {
        return new \common\models\query\ServicesQuery(get_called_class());
    }

    public static function getStatuses($value = -1){
        $statuses = array(
            1 => "Активен",
            0 => "Не активен",
        );

        if($value > -1)
            return $statuses[$value];

        return $statuses;
    }

    public static function loadList(){
        $services = (new Query())
            ->select(['id', 'name'])
            ->from(self::tableName())
            ->orderBy(["id" => SORT_ASC])
            ->all();

        return ArrayHelper::map($services, 'id', 'name');
    }

    public static function loadListWithGlobalServices(){
        $services = (new Query())
            ->select(['s.id', 's.name', 'g.name as global_name'])
            ->from('global_services as g')
            ->leftJoin('services as s', 's.global_service_id = g.id')
            ->all();

        return ArrayHelper::map($services, 'id', 'name', 'global_name');
    }

    public static function getServicesBillingIdsByIds($ids){
        if (is_array($ids)) {
            $ids = implode(', ', $ids);
        }

        $connection = Yii::$app->db;
        $services = $connection
                            ->createCommand("SELECT id, billing_id FROM services WHERE id IN (".$ids.")")
                            ->queryAll();

        return ArrayHelper::map($services, 'id', 'billing_id');
    }
}
