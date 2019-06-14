<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;

/**
 * This is the model class for table "cas_user".
 *
 * @property integer $id
 * @property string $login
 * @property integer $cas_id
 * @property string $roles
 * @property string $first_name
 * @property string $last_name
 * @property string $middle_name
 */
class ClientSearch
{   

    //перечень всех сервисов
    public $services = array();
    public $servicesForView = array();

    public $columns = array(
                            array(
                                'col' => 'abonent',
                                'source' => 'base_clients',
                                'aggregate' => 'none'
                                ),
                            array(
                                'col' => 'oper_id',
                                'source' => 'base_clients',
                                'aggregate' => 'json_agg_abonents'
                                ),
                            array(
                                'col' => 'client_id',
                                'source' => 'base_clients',
                                'aggregate' => 'json_agg_abonents'
                                ), 
                            array(
                                'col' => 'contact_phone',
                                'source' => 'base_clients',
                                'aggregate' => 'json_agg_abonents'
                                ),
                            array(
                                'col' => 'person_use_srv_as_org',
                                'source' => 'base_clients',
                                'aggregate' => 'json_agg_abonents'
                                ),
                            array(
                                'col' => 'suboper_id',
                                'source' => 'base_clients',
                                'aggregate' => 'json_agg_abonents'
                                ),
                            array(
                                'col' => 'name',
                                'source' => 'base_clients',
                                'aggregate' => 'json_agg_abonents'
                                ),
                            array(
                                'col' => 'address_jur',
                                'source' => 'base_clients',
                                'aggregate' => 'json_agg_abonents'
                                ),
                            array(
                                'col' => 'address_post',
                                'source' => 'base_clients',
                                'aggregate' => 'json_agg_abonents'
                                ),
                            array(
                                'col' => 'passport',
                                'source' => 'base_clients',
                                'aggregate' => 'json_agg_abonents'
                                ),
                            array(
                                'col' => 'inn',
                                'source' => 'base_clients',
                                'aggregate' => 'json_agg_abonents'
                                ),
                            array(
                                'col' => 'client_type',
                                'source' => 'base_clients',
                                'aggregate' => 'json_agg_abonents'
                                ),
                            array(
                                'col' => 'balance',
                                'source' => 'base_clients',
                                'aggregate' => 'json_agg_abonents'
                                ),
                            array(
                                'col' => 'service_type',
                                'source' => 'loki_basic_service',
                                'aggregate' => 'json_agg'
                                ),
                            array(
                                'col' => 'user_id',
                                'source' => 'loki_basic_service',
                                'aggregate' => 'json_agg'
                                ),
                            array(
                                'col' => 'enabled',
                                'source' => 'loki_basic_service',
                                'aggregate' => 'json_agg'
                                ),
                            array(
                                'col' => 'tariff',
                                'source' => 'loki_basic_service',
                                'aggregate' => 'json_agg'
                                ),
                            array(
                                'col' => 'address_kladr',
                                'source' => 'loki_basic_service',
                                'aggregate' => 'json_agg'
                                ),
                            array(
                                'col' => 'water_mark',
                                'source' => 'loki_basic_service',
                                'aggregate' => 'json_agg'
                                ),
                            array(
                                'col' => 'date_create',
                                'source' => 'loki_basic_service',
                                'aggregate' => 'json_agg'
                                ),
                            array(
                                'col' => 'date_expire',
                                'source' => 'loki_basic_service',
                                'aggregate' => 'json_agg'
                                ),
                            array(
                                'col' => 'tech',
                                'source' => 'loki_basic_service',
                                'aggregate' => 'json_agg'
                                ),
                            array(
                                'col' => 'id',
                                'source' => 'loki_basic_service',
                                'aggregate' => 'json_agg'
                                ),
                        );

    // Критерии для поиска
    public $criterions = array(
                            array(
                                'criterion' => 'abonent',
                                'source' => 'base_clients',
                                'descr' => 'Номер абонента',
                                'condition' => 'equal'
                                ),
                            array(
                                'criterion' => 'client_id',
                                'source' => 'base_clients',
                                'descr' => 'Лицевой счёт',
                                'condition' => 'equal'
                                ),
                            array(
                                'criterion' => 'user_id',
                                'source' => 'loki_basic_service',
                                'descr' => 'Логин',
                                'condition' => 'equal'
                                ),
                            array(
                                'criterion' => 'tariff',
                                'source' => 'loki_basic_service',
                                'descr' => 'Тарифный план',
                                'condition' => 'equal'
                                ),
                            array(
                                'criterion' => 'name',
                                'source' => 'base_clients',
                                'descr' => 'Имя/название',
                                'condition' => 'ilike'
                                ),
                            array(
                                'criterion' => 'address_jur',
                                'source' => 'base_clients',
                                'descr' => 'Юр. адрес',
                                'condition' => 'ilike'
                                ),
                            array(
                                'criterion' => 'address_post',
                                'source' => 'base_clients',
                                'descr' => 'Факт. адрес',
                                'condition' => 'ilike'
                                ),
                            /*array(
                                'criterion' => 'address_kladr',
                                'source' => 'loki_basic_service',
                                'descr' => 'Адрес оказания услуги',
                                'condition' => 'ilike'
                                ),*/
                            array(
                                'criterion' => 'passport',
                                'source' => 'base_clients',
                                'descr' => 'Номер паспорта',
                                'condition' => 'ilike'
                                ),
                            array(
                                'criterion' => 'inn',
                                'source' => 'base_clients',
                                'descr' => 'ИНН',
                                'condition' => 'ilike'
                                ),
                            array(
                                'criterion' => 'client_type',
                                'source' => 'base_clients',
                                'descr' => 'Тип клиента',
                                'condition' => 'equal'
                                ),
                            array(
                                'criterion' => 'oper_id',
                                'source' => 'base_clients',
                                'descr' => 'Провайдер',
                                'condition' => 'equal'
                                ),
                            array(
                                'criterion' => 'suboper_id',
                                'source' => 'base_clients',
                                'descr' => 'Субпровайдер',
                                'condition' => 'equal'
                                ),
                            array(
                                'criterion' => 'service_type',
                                'source' => 'loki_basic_service',
                                'descr' => 'Сервис',
                                'condition' => 'equal'
                                ),
                            
                            );

    function __construct(){
        $this->connection = \Yii::$app->db_billing;
        $this->connection_mng = \Yii::$app->db;
        $query = "SELECT name, machine_name, billing_id FROM services WHERE status = '1'";
        $this->services = $this->connection_mng
                            ->createCommand($query)
                            ->queryAll();

        $this->servicesForView = ArrayHelper::map($this->services, 'billing_id', 'name');
        $this->servicesMachineName = ArrayHelper::map($this->services, 'billing_id', 'machine_name');
        $this->services = ArrayHelper::map($this->services, 'machine_name', 'name');
    }

    public function clientSearch($dataForSearch, $page, $tab = 'both', $onlyActive){

        //  область проверки введённых данных

        foreach ($dataForSearch as $key_data => $data) {

            switch ($data['criterionForSwitch']){
                    case 'tariff':
                        if (isset($data['request']) && !empty($data['request'])) {
                            foreach ($data['request']['tariff_plan'] as $key_tariff_plan => $tariff_plan) {

                                $dataForSearch[$key_data]['request']['tariff_plan'][$key_tariff_plan]['tariff'] = trim(htmlspecialchars(pg_escape_string($tariff_plan['tariff'])));

                                $dataForSearch[$key_data]['request']['tariff_plan'][$key_tariff_plan]['oper_id'] = trim(htmlspecialchars(pg_escape_string($tariff_plan['oper_id'])));

                                if ($dataForSearch[$key_data]['request']['tariff_plan'][$key_tariff_plan]['tariff'] == '' || $dataForSearch[$key_data]['request']['tariff_plan'][$key_tariff_plan]['oper_id'] == '') {
                                    return false;
                                }
                            }
                        }
                        break;

                    case 'client_type':
                        if (isset($data['request']) && !empty($data['request'])) 
                        {
                            if (isset($data['request']['client_type']) && !empty($data['request']['client_type'])){
                                if ($data['request']['client_type'] == 'person_use_srv_as_org') {
                                    $dataForSearch[$key_data]['request']['client_type'] = trim(htmlspecialchars(pg_escape_string($data['request']['client_type'])));
                                    if ($dataForSearch[$key_data]['request']['client_type'] == '') {
                                        return false;
                                    }
                                } else {
                                    if (isset($data['request']['oper_id']) && !empty($data['request']['oper_id'])) {
                                        $dataForSearch[$key_data]['request']['client_type'] = trim(htmlspecialchars(pg_escape_string($data['request']['client_type'])));
                                        $dataForSearch[$key_data]['request']['oper_id'] = trim(htmlspecialchars(pg_escape_string($data['request']['oper_id'])));
                                        if ($dataForSearch[$key_data]['request']['oper_id'] == '' || $dataForSearch[$key_data]['request']['client_type'] == '') {
                                            return false;
                                        }
                                    } else {
                                        return false;
                                    }
                                }
                            }
                                
                        } else {
                            return false;
                        }
                        break;

                    case 'suboper_id':
                        if (isset($data['request']) && !empty($data['request'])) {
                            if (isset($data['request']['suboper_id']) && !empty($data['request']['suboper_id']) && isset($data['request']['oper_id']) && !empty($data['request']['oper_id'])) {
                                $dataForSearch[$key_data]['request']['suboper_id'] = trim(htmlspecialchars(pg_escape_string($data['request']['suboper_id'])));
                                $dataForSearch[$key_data]['request']['oper_id'] = trim(htmlspecialchars(pg_escape_string($data['request']['oper_id'])));
                                if ($dataForSearch[$key_data]['request']['oper_id'] == '' || $dataForSearch[$key_data]['request']['suboper_id'] == '') {
                                    return false;
                                }
                            } else {
                                return false;
                            }

                        } else {
                            return false;
                        }
                        break;

                    case 'address_jur':
                    case 'address_post':
                    case 'address_kladr':
                        if (isset($data['request']) && !empty($data['request'])) {
                            foreach ($data['request'] as $key_address => $address) {
                                $dataForSearch[$key_data]['request'][$key_address] = trim(htmlspecialchars(pg_escape_string($address)));
                                if ($dataForSearch[$key_data]['request'][$key_address] == '') {
                                    return false;
                                }
                            }
                        } else {
                            return false;
                        }
                        break;

                    case 'service_type':
                        if (isset($data['request']) && !empty($data['request'])) {
                            foreach ($data['request'] as $key_service => $service) {
                                $dataForSearch[$key_data]['request'][$key_service] = trim(htmlspecialchars(pg_escape_string($service)));
                                if ($dataForSearch[$key_data]['request'][$key_service] == '') {
                                    return false;
                                }
                            }
                        } else {
                            return false;
                        }
                        
                        break;

                    case 'user_id':
                        if (isset($data['request']['services']) && !empty($data['request']['services'])) {
                            $dataForSearch[$key_data]['request']['user_id'] = trim(htmlspecialchars(pg_escape_string($data['request']['user_id'])));
                            if ($dataForSearch[$key_data]['request']['user_id'] == '') {
                                return false;
                            } 

                        } else {
                            return false;
                        }
                        
                        break;

                    default:            
                        $dataForSearch[$key_data]['request'] = trim(htmlspecialchars(pg_escape_string($data['request'])));
                        if ($dataForSearch[$key_data]['request'] == '') {
                            return false;
                        }
                        break;
                   }
            
        }

        //  область генерации запроса
        $offset = 50*$page-50;

        // сбор динамических частей для запроса
        $b=1;
        $col='';
        $col_abonents='';
        $where = '';
        $group_by = '';

        // формирование списка выбираемых колонок
        $pieces = array();
        $piecesJSON = array();
        foreach ($this->columns as $key => $column) {
            if ($column['aggregate'] == 'json_agg') {
                $piecesJSON[] = $column['source'].".".$column['col'];
            } else {
                $pieces[] = $column['source'].".".$column['col']." as ".$column['col'];
            }
        }
        $col .= implode(", ", $pieces).", json_agg(row_to_json(row(".implode(", ", $piecesJSON)."))) as logins";

        $pieces = array();
        foreach ($this->columns as $key => $column) {
            if ($column['aggregate'] == 'json_agg_abonents') {
                $pieces[] = "clients.".$column['col'];
            }
        }
        $col_abonents .= implode(",", $pieces);

        // формирование предложения WHERE
        $i=1; // счетчик уровней
        foreach ($dataForSearch as $key_data => $data) {
            if ($i == 1) {
                switch ($data['criterionForSwitch']){
                    case 'tariff':
                        $where .= "(".$data['criterion']." IN (";
                        $tariffs = array();
                        foreach ($data['request']['tariff_plan'] as $key_tariff_plan => $tariff_plan) {
                            $tariffs[] = $tariff_plan['tariff'];
                        }
                        $where .= implode(", ", $tariffs);
                        $where .= "))";
                        break;

                    case 'client_type':
                        if ($data['request']['client_type'] == 'person_use_srv_as_org') {
                            $where .= "(base_clients.person_use_srv_as_org = 'true')";
                        } else {
                            $where .= "((".$data['criterion']." = '".$data['request']['client_type']."') AND (base_clients.oper_id = '".$data['request']['oper_id']."'))";
                        }
                        break;

                    case 'oper_id':
                        $where .= "(".$data['criterion']." = '".$data['request']."') ";
                        break;

                    case 'suboper_id':
                        $where .= "((".$data['criterion']." = '".$data['request']['suboper_id']."') AND (base_clients.oper_id = '".$data['request']['oper_id']."'))";
                        break;

                    case 'address_jur':
                    case 'address_post':
                        $where .= "(".$data['criterion']." ILIKE '%";
                        foreach ($data['request'] as $address) {
                            $where .= $address."%";
                        }
                        $where .= "')";
                        break;

/*                  case 'address_kladr':
                        
                        break;*/

                    case 'service_type':
                        $where .= "(".$data['criterion']." IN (";
                        $services = array();
                        foreach ($data['request'] as $key_service => $service) {
                            $services[] = "'".$service."'";
                        }
                        $where .= implode(", ", $services);
                        $where .= ")) ";
                        break;

                    case 'user_id':
                        $where .= "(loki_basic_service.service_type IN (";
                        $services = array();
                        foreach ($data['request']['services'] as $key_service => $service) {
                            $services[] = "'".$service."'";
                        }
                        $where .= implode(", ", $services);
                        $where .= ")) AND (".$data['criterion']." = '".$data['request']['user_id']."')";
                        break;

                    default:
                        $where .= "(".$data['criterion'];
                            
                        switch ($data['condition']) {
                            case 'equal':
                                $where .= " = '".$data['request']."')";
                                break;
                            case 'ilike':
                                $where .= " ILIKE '%".$data['request']."%')";
                                break;
                            default:
                                break;
                        }
                        break;
                }
            } else {
                switch ($data['criterionForSwitch']){
                    case 'tariff':
                        if ($data['searchClause'] == 'NOT') {
                            $where .= " AND ";
                        } else {
                            $where .= " ".$data['searchClause']." ";
                        }
                        if ($data['searchClause'] == 'NOT') {
                            $where .= "(".$data['criterion']." NOT IN (";
                        } else {
                            $where .= "(".$data['criterion']." IN (";
                        }
                        $tariffs = array();
                        foreach ($data['request']['tariff_plan'] as $key_tariff_plan => $tariff_plan) {
                            $tariffs[] = $tariff_plan['tariff'];
                        }
                        $where .= implode(", ", $tariffs);
                        $where .= "))";

                        break;

                    case 'client_type':
                        if ($data['searchClause'] == 'NOT') {
                            $where .= " AND (";
                        } else {
                            $where .= " ".$data['searchClause']." (";
                        }
    
                        if ($data['searchClause'] == 'NOT') {
                            if ($data['request']['client_type'] == 'person_use_srv_as_org') {
                                $where .= "base_clients.person_use_srv_as_org != true)";
                            } else {
                                $where .= "(".$data['criterion']." != '".$data['request']['client_type']."') AND (base_clients.oper_id != '".$data['request']['oper_id']."'))";
                            }
                        } else {
                            if ($data['request']['client_type'] == 'person_use_srv_as_org') {
                                $where .= "base_clients.person_use_srv_as_org = true)";
                            } else {
                                $where .= "(".$data['criterion']." = '".$data['request']['client_type']."') AND (base_clients.oper_id = '".$data['request']['oper_id']."'))";
                            }
                        }
                        break;
                    case 'oper_id':
                        if ($data['searchClause'] == 'NOT') {
                            $where .= " AND (";
                        } else {
                            $where .= " ".$data['searchClause']." (";
                        }
    
                        if ($data['searchClause'] == 'NOT') {
                            $where .= "(".$data['criterion']." != '".$data['request']."')) ";
                        } else {
                            $where .= "(".$data['criterion']." = '".$data['request']."')) ";
                        }
                        break;

                    case 'suboper_id':
                        if ($data['searchClause'] == 'NOT') {
                            $where .= " AND (";
                        } else {
                            $where .= " ".$data['searchClause']." (";
                        }
    
                        if ($data['searchClause'] == 'NOT') {
                            $where .= "(".$data['criterion']." != '".$data['request']['suboper_id']."') AND (base_clients.oper_id != '".$data['request']['oper_id']."'))";
                        } else {
                            $where .= "(".$data['criterion']." = '".$data['request']['suboper_id']."') AND (base_clients.oper_id = '".$data['request']['oper_id']."'))";
                        }
                        break;

                    case 'address_jur':
                    case 'address_post':
                    /*case 'address_kladr':*/
                        if ($data['searchClause'] == 'NOT') {
                            $where .= " AND (";
                        } else {
                            $where .= " ".$data['searchClause']." (";
                        }
                        
                        $where .= $data['criterion'];
                        
                        if ($data['searchClause'] == 'NOT') {
                            $where .= " NOT ILIKE '%";
                            foreach ($data['request'] as $address) {
                                $where .= $address."%";
                            }
                            $where .= "') ";
                        } else {
                            $where .= " ILIKE '%";
                            foreach ($data['request'] as $address) {
                                $where .= $address."%";
                            }
                            $where .= "') ";
                        }               
                        break;

                    case 'service_type':
                        

                        if ($data['searchClause'] == 'NOT') {
                            $where .= " AND ";
                            $where .= "(".$data['criterion']." NOT IN (";
                        } else {
                            $where .= " ".$data['searchClause']." ";
                            $where .= "(".$data['criterion']." IN (";
                        }
                        
                        $i=1;
                        foreach ($data['request'] as $key_service => $service) {
                            if ($i == count($data['request'])) {
                                $where .= "'".$service."')) ";
                            } else {
                                $where .= "'".$service."', ";
                            }
                            $i++;
                        }
                        break;

                    case 'user_id':

                        if ($data['searchClause'] == 'NOT') {
                            $where .= " AND ";
                            $where .= "((".$data['criterion']." != '".$data['request']['user_id']."') AND ";
                            $where .= "(loki_basic_service.service_type NOT IN (";
                        } else {
                            $where .= " ".$data['searchClause']." ";
                            $where .= "((".$data['criterion']." = '".$data['request']['user_id']."') AND ";
                            $where .= "(loki_basic_service.service_type IN (";
                        }

                        $i=1;
                        foreach ($data['request']['services'] as $key_service => $service) {
                            if ($i == count($data['request']['services'])) {
                                $where .= "'".$service."')) ";
                            } else {
                                $where .= "'".$service."', ";
                            }
                            $i++;
                        }
                        $where .= ") ";
                        break;

                    default:
                        if ($data['searchClause'] == 'NOT') {
                            $where .= " AND (";
                        } else {
                            $where .= " ".$data['searchClause']." (";
                        }
                        
                        $where .= $data['criterion'];

                        switch ($data['condition']) {
                            case 'equal':
                                if ($data['searchClause'] == 'NOT') {
                                    $where .= " != '".$data['request']."') ";
                                } else {
                                    $where .= " = '".$data['request']."') ";
                                }
                                break;
                            case 'ilike':
                                if ($data['searchClause'] == 'NOT') {
                                    $where .= " NOT ILIKE '%".$data['request']."%') ";
                                } else {
                                    $where .= " ILIKE '%".$data['request']."%') ";
                                }
                                break;
                            default:
                                break;
                        }
                        break;
                }
            }
            $i++;
        };


        // формирование предложения GROUP BY
        $pieces = array();
        foreach ($this->columns as $key => $column) {
            if ($column['aggregate'] != 'json_agg') {
                $pieces[] = $column['source'].".".$column['col'];
            }
        }

        $group_by = implode(", ", $pieces)." ";


        // формирование непосредственно запроса
        $query_clients = "SELECT ".$col." 
                            FROM base_clients 
                            LEFT JOIN loki_basic_service ON loki_basic_service.base_client_id = base_clients.id
                            WHERE base_clients.abonent IS NULL AND base_clients.id IN (SELECT base_clients.id FROM base_clients LEFT JOIN loki_basic_service ON loki_basic_service.base_client_id = base_clients.id WHERE ".$where.")
                            GROUP BY ".$group_by;

        $query_clients_count = "SELECT count(abonent.client_id) as count FROM (".$query_clients.") as abonent;";

        $query_clients .= " LIMIT 50 OFFSET ".$offset.";";



        $query_abonents = "SELECT clients.abonent as abonent, json_agg(row_to_json(row(".$col_abonents.", clients.logins))) as clients 
                                FROM (
                                    SELECT ".$col."
                                    FROM base_clients 
                                    LEFT JOIN loki_basic_service ON loki_basic_service.base_client_id = base_clients.id
                                    WHERE base_clients.abonent IS NOT NULL AND base_clients.abonent IN (SELECT base_clients.abonent FROM base_clients LEFT JOIN loki_basic_service ON loki_basic_service.base_client_id = base_clients.id WHERE ".$where.")
                                    GROUP BY ".$group_by."
                                    ) as clients
                                GROUP BY clients.abonent";

        $query_abonents_count = "SELECT count(abonent.abonent) as count FROM (".$query_abonents.") as abonent;";

        $query_abonents .= " LIMIT 50 OFFSET ".$offset.";";

        switch ($tab) {
            case 'both':
                $results['clients'] = $this->connection
                                    ->createCommand($query_clients)
                                    ->queryAll();
                $results['abonents'] = $this->connection
                                    ->createCommand($query_abonents)
                                    ->queryAll();

                $results['count_clients'] = $this->connection
                                    ->createCommand($query_clients_count)
                                    ->queryAll();
                $results['count_abonents'] = $this->connection
                                    ->createCommand($query_abonents_count)
                                    ->queryAll();
                break;
            
            case 'abonents':
                $results['abonents'] = $this->connection
                                    ->createCommand($query_abonents)
                                    ->queryAll();
                $results['count_abonents'] = $this->connection
                                    ->createCommand($query_abonents_count)
                                    ->queryAll();
                break;

            case 'clients':
                $results['clients'] = $this->connection
                                    ->createCommand($query_clients)
                                    ->queryAll();
                $results['count_clients'] = $this->connection
                                    ->createCommand($query_clients_count)
                                    ->queryAll();
                break;
            default:
                break;
        }

        if (isset($results['clients']) && !empty($results['clients'])) {
            foreach ($results['clients'] as $key => $result) {
                $results['clients'][$key]['logins'] = json_decode($result['logins'], TRUE);
            }
            $results['clients'] = self::rewritesearchResultForHuman($results['clients']);       
            $results['clients'] = self::rewritesearchResultServices($results['clients']);
        }

        if (isset($results['abonents']) && !empty($results['abonents'])) {
            foreach ($results['abonents'] as $key => $result) {
                $results['abonents'][$key]['clients'] = json_decode($result['clients'], TRUE);
            }
            $results['abonents'] = self::rewritesearchResultForHuman($results['abonents']); 
            $results['abonents'] = self::rewritesearchResultServices($results['abonents']);
        }

        return $results;
    } 

    // метод добавляет в массив с результатами поиска человекопонятные описания сервисов, операторов и т.д. (всё что попадает в результаты только с машинным именем)
    public function rewritesearchResultForHuman($data){
        $opers = self::getProviders();
        $subopers = self::getSubproviders();
        $client_types = self::getClientTypes();
        $tariff_plans = self::getTariffPlans();     

        foreach ($data as $key_result => $result) {
            
            if (isset($result['abonent']) && !empty($result['abonent'])) {
                $data[$key_result]['abonent_name'] = $this->connection
                                    ->createCommand("SELECT name FROM abonent WHERE id = '".$result['abonent']."';")
                                    ->queryScalar();
            }
            
            if (isset($result['logins']) && !empty($result['logins'])) {
                foreach ($opers as $key_oper => $oper) {
                    if ($oper['oper_id'] == $result['oper_id']) {
                        $data[$key_result]['oper_descr'] = $oper['name'];
                    }
                }

                foreach ($subopers as $key_soper => $oper) {
                    if ($oper['oper_id'] == $result['oper_id']) {
                        foreach ($oper['json_agg'] as $key_suboper => $suboper) {
                            if ($suboper['f1'] == $result['suboper_id']) {
                                $data[$key_result]['suboper_descr'] = $suboper['f2'];
                            }
                        }
                    }
                }

                foreach ($client_types as $key_soper => $oper) {
                    if ($oper['oper_id'] == $result['oper_id']) {
                        foreach ($oper['json_agg'] as $key_slient_type => $slient_type) {
                            if ($slient_type['f1'] == $result['client_type']) {
                                $data[$key_result]['client_type_descr'] = $slient_type['f2'];
                            }
                        }
                    }
                }
                
                foreach ($result['logins'] as $key_client_service => $client_service) {
                    foreach ($tariff_plans as $key_service => $service) {
                        foreach ($service['tariffs'] as $key_tariffs => $tariffs) {
                            if(isset($tariffs) && !empty($tariffs)){
                                foreach ($tariffs['tariffs'] as $key_tariff => $tariff) {        
                                    if ($tariff['f1'] == $client_service['f4']) {
                                        $data[$key_result]['logins'][$key_client_service]['tariff_plan_descr'] = $tariff['f3'];
                                    }
                                }
                            }
                        }
                    }

                    // disable reason
                    $data[$key_result]['logins'][$key_client_service]['disable_reason'] = 'enable';

                    if (($client_service['f7'] != '') && (strtotime($client_service['f7']) <= time())) {

                        $data[$key_result]['logins'][$key_client_service]['disable_reason'] = 'agreement closed';
                    } elseif ($result['balance'] <= $client_service['f6']) {
                        $data[$key_result]['logins'][$key_client_service]['disable_reason'] = 'no money';
                    }
                }
            } elseif (isset($result['clients']) && !empty($result['clients']))  {
                foreach ($result['clients'] as $key_client => $client) {
                    foreach ($opers as $key_oper => $oper) {
                        if ($oper['oper_id'] == $client['f1']) {
                            $data[$key_result]['clients'][$key_client]['oper_descr'] = $oper['name'];
                        }
                    }

                    foreach ($subopers as $key_soper => $oper) {
                        if ($oper['oper_id'] == $client['f1']) {
                            foreach ($oper['json_agg'] as $key_suboper => $suboper) {
                                if ($suboper['f1'] == $client['f5']) {
                                    $data[$key_result]['clients'][$key_client]['suboper_descr'] = $suboper['f2'];
                                }
                            }
                        }
                    }

                    foreach ($client_types as $key_soper => $oper) {
                        if ($oper['oper_id'] == $client['f1']) {
                            foreach ($oper['json_agg'] as $key_slient_type => $client_type) {
                                if ($client_type['f1'] == $client['f11']) {
                                    $data[$key_result]['clients'][$key_client]['client_type_descr'] = $client_type['f2'];
                                }
                            }
                        }
                    }
                    
                    foreach ($client['f13'] as $key_client_service => $client_service) {
                        foreach ($tariff_plans as $key_service => $service) {
                            foreach ($service['tariffs'] as $key_tariffs => $tariffs) {
                                if(isset($tariffs) && !empty($tariffs)){
                                    foreach ($tariffs['tariffs'] as $key_tariff => $tariff) {        
                                        if ($tariff['f1'] == $client_service['f4']) {
                                            $data[$key_result]['clients'][$key_client]['f13'][$key_client_service]['tariff_plan_descr'] = $tariff['f3'];
                                        }
                                    }
                                }
                            }
                        }

                        // disable reason
                        $data[$key_result]['clients'][$key_client]['f13'][$key_client_service]['disable_reason'] = 'enable';

                        if (($client_service['f8'] != '') && (strtotime($client_service['f8']) <= time())) {
                            $data[$key_result]['clients'][$key_client]['f13'][$key_client_service]['disable_reason'] = 'agreement closed';
                        } elseif ($client['f12'] <= $client_service['f6']) {
                            $data[$key_result]['clients'][$key_client]['f13'][$key_client_service]['disable_reason'] = 'no money';
                        }
                    }
                }
            }

        }

        return $data;       
    }

    //группирует сервисы
    public function rewritesearchResultServices($data){
        foreach ($data as $key_result => $result) {

            if (isset($result['logins']) && !empty($result['logins'])) {
                foreach ($result['logins'] as $key_client_service => $client_service) {
                    if (isset($this->servicesMachineName[$client_service['f1']]) && !empty($this->servicesMachineName[$client_service['f1']])) {
                       if (!(isset($data[$key_result]['logins'][$this->servicesMachineName[$client_service['f1']]]))) {

                            $data[$key_result]['logins'][$this->servicesMachineName[$client_service['f1']]] = array();
                            $data[$key_result]['logins'][$this->servicesMachineName[$client_service['f1']]]['logins'] = array();
                            $data[$key_result]['logins'][$this->servicesMachineName[$client_service['f1']]]['service_descr'] = $this->servicesForView[$client_service['f1']];
                        } 

                        $data[$key_result]['logins'][$this->servicesMachineName[$client_service['f1']]]['logins'][] = $client_service;
                        unset($data[$key_result]['logins'][$key_client_service]);
                    } else {
                        unset($data[$key_result]['logins'][$key_client_service]);
                    }
                }     
            }
            elseif (isset($result['clients']) && !empty($result['clients']))  {
                foreach ($result['clients'] as $key_client => $client) {
                    if (isset($client['f13']) && !empty($client['f13'])) {
                        foreach ($client['f13'] as $key_service => $service) {
                            if (isset($this->servicesMachineName[$service['f1']]) && !empty($this->servicesMachineName[$service['f1']])) {
                                if (!(isset($data[$key_result]['clients'][$key_client]['f13'][$this->servicesMachineName[$service['f1']]]))) {
                                    $data[$key_result]['clients'][$key_client]['f13'][$this->servicesMachineName[$service['f1']]] = array();
                                    $data[$key_result]['clients'][$key_client]['f13'][$this->servicesMachineName[$service['f1']]]['logins'] = array();
                                    $data[$key_result]['clients'][$key_client]['f13'][$this->servicesMachineName[$service['f1']]]['service_descr'] = $this->servicesForView[$service['f1']];
                                } 

                                $data[$key_result]['clients'][$key_client]['f13'][$this->servicesMachineName[$service['f1']]]['logins'][] = $service;
                                unset($data[$key_result]['clients'][$key_client]['f13'][$key_service]);
                            } else {
                                unset($data[$key_result]['clients'][$key_client]['f13'][$key_service]);
                            }
                        }
                    }
                }
                
            }
        }

        return $data;
    }

    public function getTariffPlans(){
        $results = array();

        foreach ($this->servicesForView as $billing_id => $service) {
            $results[$billing_id]['tariffs'] = $this->connection
                ->createCommand("SELECT 
                                  loki_tariff_plan.provider_id,
                                  provider.name,
                                  provider.operid,
                                  json_agg(to_json(row(loki_tariff_plan.id, loki_tariff_plan.cherry_id, loki_tariff_plan.name))) as tariffs
                                FROM 
                                  public.loki_tariff_plan, 
                                  public.provider
                                WHERE 
                                  loki_tariff_plan.provider_id = provider.id AND loki_tariff_plan.service_type = '".$billing_id."'
                                GROUP BY loki_tariff_plan.provider_id, provider.name, provider.operid;")
                ->queryAll();
            $results[$billing_id]['service_name'] = $service;
        }

        foreach ($results as $key_result => $result) {
            
            foreach ($result['tariffs'] as $key_tariff => $tariff) {
                $results[$key_result]['tariffs'][$key_tariff]['name'] = htmlspecialchars($tariff['name']);
                $results[$key_result]['tariffs'][$key_tariff]['tariffs'] = json_decode($tariff['tariffs'], true);
            }
        }

        return $results;
    }

    public function getProviders(){
        $results = $this->connection
                ->createCommand('SELECT oper_id, name FROM base_providers')
                ->queryAll();

        return $results;
    }

    public function getSubproviders(){
        $results = $this->connection
                ->createCommand('SELECT bp.oper_id, bp.name, json_agg(to_json(row(bs.suboper_id, bs.name))) FROM base_subproviders bs
        LEFT JOIN base_providers bp ON bp.oper_id = bs.oper_id GROUP BY bp.oper_id, bp.name')
                ->queryAll();

        foreach ($results as $key => $result) {
            $results[$key]['json_agg'] = json_decode($result['json_agg'], true);
            $results[$key]['name'] = htmlspecialchars($result['name']);
        }

        return $results;
    }

    public function getClientTypes(){
        $results = $this->connection
                ->createCommand('SELECT bp.oper_id, bp.name, json_agg(to_json(row(bct.client_type, bct.descr))) FROM base_client_types bct
        LEFT JOIN base_providers bp ON bp.oper_id = bct.oper_id GROUP BY bp.oper_id, bp.name')
                ->queryAll();

        foreach ($results as $key => $result) {
            $results[$key]['json_agg'] = json_decode($result['json_agg'], true);
            $results[$key]['name'] = htmlspecialchars($result['name']);
        }
        
        return $results;
    }

    public function searchOneAbonent($abonent_id){

        $abonent_id = trim(htmlspecialchars(pg_escape_string($abonent_id)));
        if ($abonent_id == '') {
            return false;
        }

        $abonent = array();

        /* Получение информации об абоненте */
        $query = "SELECT id, name FROM abonent WHERE id = '".$abonent_id."'";
        $abonent['abonent'] = $this->connection
                ->createCommand($query)
                ->queryOne();
           
        $query = "SELECT ";
        $columns = array();
        foreach ($this->columns as $key_column => $column) {
            if ($column['source'] == 'base_clients') {
                $columns[] = $column['source'].".".$column['col']; 
            } 
        }
        $columns = implode(", ", $columns);

        $query .= $columns.", base_clients.id, base_providers.name as provider, base_subproviders.name as subprovider, base_client_types.descr as client_type_descr FROM base_clients 
                    LEFT JOIN base_providers ON base_providers.oper_id = base_clients.oper_id
                    LEFT JOIN base_subproviders ON (base_subproviders.oper_id = base_clients.oper_id AND base_subproviders.suboper_id = base_clients.suboper_id)
                    LEFT JOIN base_client_types ON (base_client_types.oper_id = base_clients.oper_id AND base_client_types.client_type = base_clients.client_type)
                    WHERE base_clients.abonent = '".$abonent_id."'";

        $abonent['base_clients'] = $this->connection
                ->createCommand($query)
                ->queryAll();

        foreach ($abonent['base_clients'] as $key_client_id => $client_id) {
            foreach ($this->servicesForView as $key_service => $service) {
                $query = "SELECT ";

                $columns = array();
                foreach ($this->columns as $key_column => $column) {
                    if ($column['source'] == 'loki_basic_service') {
                        if ($column['col'] == 'address_kladr') {
                            $columns[] = "get_full_address(".$column['col'].") as address_kladr";
                        } else {
                            $columns[] = $column['source'].".".$column['col'];
                        }
                    } 
                }
                $columns = implode(", ", $columns);

                $query .= $columns.", loki_tariff_plan.name as tariff_plan, service_tech.name as connect_tech 
                                    FROM loki_basic_service 
                                    LEFT JOIN loki_tariff_plan ON loki_tariff_plan.id = loki_basic_service.tariff 
                                    LEFT JOIN service_tech ON service_tech.id = loki_basic_service.tech 
                                    WHERE loki_basic_service.base_client_id = '".$client_id['id']."' AND loki_basic_service.service_type = '".$key_service."';";

                $results = $this->connection
                                ->createCommand($query)
                                ->queryAll();

                if (isset($results) && !empty($results)) {
                    $abonent['base_clients'][$key_client_id]['services'][$service] = $results;
                    $abonent['base_clients'][$key_client_id]['services'][$service]['service_type'] = $key_service;
                }
            }
        }

        return $abonent;
    }

    public function searchOneClient($client_id){
        $client_id = trim(htmlspecialchars(pg_escape_string($client_id)));
        if ($client_id == '') {
            return false;
        }

        $client = array();

        /* Получение информации о клиенте */
           
        $query = "SELECT ";
        $columns = array();
        foreach ($this->columns as $key_column => $column) {
            if ($column['source'] == 'base_clients') {
                $columns[] = $column['source'].".".$column['col'];
            } 
        }
        $columns = implode(", ", $columns);

        $query .= $columns.", base_clients.id, base_providers.name as provider, base_subproviders.name as subprovider, base_client_types.descr as client_type_descr FROM base_clients 
                    LEFT JOIN base_providers ON base_providers.oper_id = base_clients.oper_id
                    LEFT JOIN base_subproviders ON (base_subproviders.oper_id = base_clients.oper_id AND base_subproviders.suboper_id = base_clients.suboper_id)
                    LEFT JOIN base_client_types ON (base_client_types.oper_id = base_clients.oper_id AND base_client_types.client_type = base_clients.client_type)
                    WHERE base_clients.client_id = '".$client_id."'";

        $client = $this->connection
                ->createCommand($query)
                ->queryOne();

        foreach ($this->servicesForView as $key_service => $service) {
            $query = "SELECT ";

            $columns = array();
            foreach ($this->columns as $key_column => $column) {
                if ($column['source'] == 'loki_basic_service') {
                    if ($column['col'] == 'address_kladr') {
                        $columns[] = "get_full_address(".$column['col'].") as address_kladr";
                    } else {
                        $columns[] = $column['source'].".".$column['col'];
                    }
                } 
            }
            $columns = implode(", ", $columns);

            $query .= $columns.", loki_tariff_plan.name as tariff_plan, service_tech.name as connect_tech  
                                FROM loki_basic_service 
                                LEFT JOIN loki_tariff_plan ON loki_tariff_plan.id = loki_basic_service.tariff 
                                LEFT JOIN service_tech ON service_tech.id = loki_basic_service.tech 
                                WHERE loki_basic_service.base_client_id = '".$client['id']."' AND loki_basic_service.service_type = '".$key_service."';";

            $results = $this->connection
                            ->createCommand($query)
                            ->queryAll();

            if (isset($results) && !empty($results)) {
                $client['services'][$service] = $results;
                $client['services'][$service]['service_type'] = $key_service;
            }
        }

        return $client;
    }

    public function searchByLokiBasicService($loki_basic_service_id){
        if (empty($loki_basic_service_id) || !is_array($loki_basic_service_id) || !isset($loki_basic_service_id)) {
            throw new InvalidConfigException('Передан неверный формат данных');
        }
        foreach ($loki_basic_service_id as $key => $id) {
            $loki_basic_service_id[$key] = (int)$id;
        }

        $loki_basic_service_id = implode(', ', $loki_basic_service_id);

        if ($loki_basic_service_id == '') {
            throw new InvalidConfigException('Пустое условие для запроса');
        }

        $client = array();

        /* Получение информации о сервисе */

        $query = "SELECT ";

        $columns = array();
        foreach ($this->columns as $key_column => $column) {
            if ($column['source'] == 'loki_basic_service') {
                if ($column['col'] == 'address_kladr') {
                    $columns[] = "get_full_address(".$column['col'].") as address_kladr";
                } else {
                    $columns[] = $column['source'].".".$column['col'];
                }
            } 
            if ($column['source'] == 'base_clients') {
                $columns[] = $column['source'].".".$column['col'];
            }
        }
        $columns = implode(", ", $columns);

        $query .= $columns.", loki_tariff_plan.name as tariff_plan, service_tech.name as connect_tech, loki_basic_service.base_client_id, 
                                base_clients.id  as base_clients__id, base_providers.name as provider, base_subproviders.name as subprovider, 
                                base_client_types.descr as client_type_descr, loki_basic_service.id as loki_basic_service__id
                            FROM loki_basic_service 
                            LEFT JOIN loki_tariff_plan ON loki_tariff_plan.id = loki_basic_service.tariff 
                            LEFT JOIN service_tech ON service_tech.id = loki_basic_service.tech 
                            LEFT JOIN base_clients ON base_clients.id = loki_basic_service.base_client_id
                            LEFT JOIN base_providers ON base_providers.oper_id = base_clients.oper_id
                            LEFT JOIN base_subproviders ON (base_subproviders.oper_id = base_clients.oper_id AND base_subproviders.suboper_id = base_clients.suboper_id)
                            LEFT JOIN base_client_types ON (base_client_types.oper_id = base_clients.oper_id AND base_client_types.client_type = base_clients.client_type)
                            WHERE loki_basic_service.id IN (".$loki_basic_service_id.")
                            LIMIT 100;";

        $client = $this->connection
                        ->createCommand($query)
                        ->queryAll();

        return ArrayHelper::index($client, 'loki_basic_service__id');
    }
}
