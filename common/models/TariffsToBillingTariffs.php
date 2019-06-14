<?php

namespace common\models;

use Yii;
use common\models\history\TariffsToBillingTariffsHistory;
use yii\helpers\ArrayHelper;
use common\models\Tariffs;

class TariffsToBillingTariffs extends \yii\db\ActiveRecord
{
    public $updater;
    public $updated_at;
    
    public static function tableName()
    {
        return 'tariffs_to_billing_tariffs';
    }

    public function rules()
    {
        return [
            [['tariff_id', 'billing_id', 'publication_status', 'created_at', 'cas_user_id'], 'required'],
            [['tariff_id', 'billing_id', 'publication_status', 'created_at', 'cas_user_id', 'updater', 'updated_at'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tariff_id' => 'Tariff ID',
            'billing_id' => 'Billing ID',
            'publication_status' => 'Publication Status',
            'created_at' => 'Created At',
            'cas_user_id' => 'Cas User ID',
        ];
    }

    public static function find()
    {
        return new \common\models\query\TariffsToBillingTariffsQuery(get_called_class());
    }

    public function afterSave($insert, $changedAttributes){
        $history = new TariffsToBillingTariffsHistory();
        $history->setAttributes($this->getAttributes());
        $history->origin_id = $this->id;
        if (!$insert) {
           $history->created_at = $this->updated_at;
           $history->cas_user_id = $this->updater;
        }
        $history->save();
    }

    public static function getBillingTariffsForTariff($tariff_id, $publication_status = false)
    {
        $connection = Yii::$app->db;
        $where_status = '';
        if ($publication_status) {
            $where_status = ' AND publication_status = '.$publication_status;
        }
        $services = $connection
                            ->createCommand("SELECT id, billing_id FROM ".self::tableName()." WHERE tariff_id = '".$tariff_id."'".$where_status)
                            ->queryAll();

        return ArrayHelper::map($services, 'id', 'billing_id');
    }

    public static function getBillingIdsForTariff($tariff_id, $publication_status = false){
        $where_status = '';
        if ($publication_status) {
            $where_status = ' AND publication_status = '.$publication_status;
        }
        $connection = Yii::$app->db;
        $services = $connection
                            ->createCommand("SELECT billing_id FROM tariffs_to_billing_tariffs WHERE tariff_id = '".$tariff_id."'".$where_status)
                            ->queryAll();

        return ArrayHelper::getColumn($services, 'billing_id');
    }

    public static function getBillingIdsListById($id){
        $ids = self::getBillingIdsForTariff($id, 1);
        $in = implode(', ', $ids);
        $connection = Yii::$app->db_billing;
        $services = $connection
                            ->createCommand("SELECT id, name FROM loki_tariff_plan WHERE id IN (".$in.")")
                            ->queryAll();

        return ArrayHelper::map($services, 'id', 'name');
    }

    public static function getTariffsFromBillingByServices($services){
        $services = Services::getServicesBillingIdsByIds($services);
        $services = implode(', ', $services);

        $connection = Yii::$app->db_billing;
        $tariffs = $connection
                        ->createCommand("SELECT 
                                  loki_tariff_plan.provider_id,
                                  loki_tariff_plan.state,
                                  provider.name as oper,
                                  service_type.name as service,
                                  loki_tariff_plan.service_type,
                                  json_agg(to_json(row(loki_tariff_plan.id, loki_tariff_plan.name))) as tariffs
                                FROM 
                                  public.loki_tariff_plan, 
                                  public.provider,
                                  public.service_type
                                WHERE 
                                  loki_tariff_plan.provider_id = provider.id AND loki_tariff_plan.service_type = service_type.id AND loki_tariff_plan.service_type IN ({$services})
                                GROUP BY loki_tariff_plan.provider_id, provider.name, service_type.name, loki_tariff_plan.service_type, loki_tariff_plan.state
                                ORDER BY loki_tariff_plan.state DESC;")
                        ->queryAll();

        $for_map = array();
        $i = 0;
        foreach ($tariffs as $key_service_type => $service_type) {
            $tariffs[$key_service_type]['tariffs'] = json_decode($service_type['tariffs'], true);
            foreach ($tariffs[$key_service_type]['tariffs'] as $key_tariff => $tariff) {
                $for_map[$i]['optgroup'] = $service_type['service'].': '.$tariffs[$key_service_type]['oper'].' ('.Tariffs::$billing_tariff_state[$service_type['state']].')';
                $for_map[$i]['tariff_id'] = $tariffs[$key_service_type]['tariffs'][$key_tariff]['f1'];
                $for_map[$i]['tariff_name'] = $tariffs[$key_service_type]['tariffs'][$key_tariff]['f2'];
                $i++;
            }
        }
    
        return ArrayHelper::map($for_map, 'tariff_id', 'tariff_name', 'optgroup');
    }


/*    public static function getTariffsFromBilling(){
        $connection = Yii::$app->db_billing;
        $tariffs = $connection
                        ->createCommand("SELECT 
                                  loki_tariff_plan.provider_id,
                                  provider.name as oper,
                                  service_type.name as service,
                                  loki_tariff_plan.service_type,
                                  json_agg(to_json(row(loki_tariff_plan.id, loki_tariff_plan.name))) as tariffs
                                FROM 
                                  public.loki_tariff_plan, 
                                  public.provider,
                                  public.service_type
                                WHERE 
                                  loki_tariff_plan.provider_id = provider.id AND loki_tariff_plan.service_type = service_type.id
                                GROUP BY loki_tariff_plan.provider_id, provider.name, service_type.name, loki_tariff_plan.service_type
                                ORDER BY loki_tariff_plan.service_type, provider.name ASC;")
                        ->queryAll();

        $for_map = array();
        $i = 0;
        foreach ($tariffs as $key_service_type => $service_type) {
            $tariffs[$key_service_type]['tariffs'] = json_decode($service_type['tariffs'], true);
            foreach ($tariffs[$key_service_type]['tariffs'] as $key_tariff => $tariff) {
                $for_map[$i]['optgroup'] = $service_type['service'].': '.$tariffs[$key_service_type]['oper'];
                $for_map[$i]['tariff_id'] = $tariffs[$key_service_type]['tariffs'][$key_tariff]['f1'];
                $for_map[$i]['tariff_name'] = $tariffs[$key_service_type]['tariffs'][$key_tariff]['f2'];
                $i++;
            }
        }
    
        return ArrayHelper::map($for_map, 'tariff_id', 'tariff_name', 'optgroup');
    }*/

    





    

    
}
