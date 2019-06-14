<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\db\Query;

class ConnectionTechnologies extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'connection_technologies';
    }

    public function rules()
    {
        return [
            [['name', 'service_id'], 'required'],
            [['comment'], 'string'],
            [['comment', 'name'], 'trim'],
            [['service_id', 'billing_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'comment' => 'Комментарий',
            'service_id' => 'Сервис',
            'billing_id' => 'ID в биллинге'
        ];
    }

    public function getService()
    {
        return $this->hasOne(Services::className(), ['id' => 'service_id']);
    }

    public static function find()
    {
        return new \common\models\query\ConnectionTechnologiesQuery(get_called_class());
    }

    public static function getServices(){
        $services = (new Query())
            ->select(['s.id', 's.name', 'g.name as global_name'])
            ->from('global_services as g')
            ->leftJoin('services as s', 's.global_service_id = g.id')
            ->all();

        return ArrayHelper::map($services, 'id', 'name', 'global_name');
    }

    public static function getConntechsByServiceBillingId($service_billing_id){
        return (new Query())
            ->select(['ct.name as name', 'ct.id as id', 'ct.billing_id as billing_id'])
            ->from('connection_technologies as ct')
            ->leftJoin('services as s', 's.id = ct.service_id')
            ->where(["s.billing_id" => $service_billing_id])
            ->all();
    }

    public static function getTechnologiesList($services = '', $whitout_services = false){
        if ($services == '') {
            $all_services = Services::loadList();
            foreach ($all_services as $key => $service) {
                $services[] = $key;
            }
        }
        if (is_array($services)) {
            $services = implode(', ', $services);
        }
        $connection = Yii::$app->db;
        if ($whitout_services) {
            $results = $connection
                            ->createCommand("
                                SELECT id, name
                                FROM connection_technologies 
                                WHERE service_id IN ({$services})
                            ")
                            ->queryAll();

            $results = ArrayHelper::map($results, 'id', 'name');
        } else {
            $results = $connection
                            ->createCommand("
                                SELECT s.name as service, json_agg(row_to_json(row(ct.id, ct.name))) as technologies
                                FROM connection_technologies ct
                                LEFT JOIN services s ON ct.service_id = s.id
                                WHERE ct.service_id IN ({$services})
                                GROUP BY s.name
                            ")
                            ->queryAll();

            $results = ArrayHelper::map($results, 'service', 'technologies');

            foreach ($results as $key => $result) {
                $result = json_decode($result, true);
                $results[$key] = ArrayHelper::map($result, 'f1', 'f2');
            }
        }

        return $results;
    }
}
