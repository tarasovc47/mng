<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use common\components\Dadata;

class AddressesRecycle extends \yii\db\ActiveRecord
{
    public static $conclusion = [
        0 => 'Проверка невозможна (недостаточно данных, или они некорректны)',
        1 => 'Полное соответствие (автоматическая проверка)',
        2 => 'Требуется участие оператора',
        3 => 'Заносим предложенный адрес в биллинг (ручная проверка)',
        4 => 'Необходима ручная обработка',
        5 => 'Не совпадают города',
        6 => 'По ИНН найдено несколько компаний',
        7 => 'Является ИП, анализ не проводим',
    ];

    public static $actual_status = [
        0 => 'Действующая',
        1 => 'Ликвидируется',
        2 => 'Ликвидирована',
        -1 => 'Не удалось определить',
    ];

    public static function tableName()
    {
        return 'addresses_recycle';
    }

    public function rules()
    {
        return [
            [['billing_base_clients_id'], 'required'],
            [['billing_base_clients_id', 'dadata_clean_address_status', 'conclusion_address', 'conclusion_company_name', 'actual_company'], 'integer'],
            [['billing_base_clients_client_id', 'billing_base_clients_oper_id', 'billing_company_name', 'billing_company_address_jur', 'dadata_clean_address', 'dadata_suggest_address', 'dadata_suggest_company_name', 'billing_company_inn', 'dadata_clean_address_fias_id', 'dadata_suggest_address_fias_id', 'postcode'], 'string', 'max' => 255],
            [['billing_base_clients_client_id', 'billing_base_clients_oper_id', 'billing_company_name', 'billing_company_address_jur', 'dadata_clean_address', 'dadata_suggest_address', 'dadata_suggest_company_name', 'billing_company_inn', 'dadata_clean_address_fias_id', 'dadata_suggest_address_fias_id', 'postcode'], 'trim'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'billing_base_clients_id' => 'Billing Base Clients ID',
            'billing_base_clients_client_id' => 'Лицевой счёт',
            'billing_base_clients_oper_id' => 'Оператор',
            'billing_company_name' => 'Название',
            'billing_company_inn' => 'ИНН',
            'billing_company_address_jur' => 'Юридический адрес',
            'dadata_clean_address' => 'Очищенный адрес',
            'dadata_clean_address_fias_id' => 'Очищенный адрес fias_id',
            'dadata_clean_address_status' => 'Статус очистки адреса',
            'dadata_suggest_address' => 'Предложенный адрес',
            'dadata_suggest_address_fias_id' => 'Предложенный адрес fias_id',
            'dadata_suggest_company_name' => 'Предложенное название',
            'conclusion_address' => 'Заключение по адресу',
            'conclusion_company_name' => 'Заключение по названию',
            'actual_company' => 'Актуальность компании',
        ];
    }

    public static function find()
    {
        return new \common\models\query\AddressesRecycleQuery(get_called_class());
    }

    public function loadCompanyInfo(){
        $connection = Yii::$app->db_billing;
        $company = $connection
                            ->createCommand("
                                SELECT id, client_id, oper_id, name, inn, address_jur
                                FROM base_clients
                                WHERE id = {$this->billing_base_clients_id}
                            ")
                            ->queryOne();

        if (isset($company) && !empty($company)) {
            $this->billing_base_clients_client_id = $company['client_id'];
            $this->billing_base_clients_oper_id = $company['oper_id'];
            $this->billing_company_name = $company['name'];
            $inn = mb_stristr($company['inn'], '/', true);
            $this->billing_company_inn = ($inn) ? $inn : $company['inn'];
            $this->billing_company_inn = trim($this->billing_company_inn);
            $this->billing_company_address_jur = trim($company['address_jur']);

            return true;
        }

        return false;        
    }

    public function isCompanyActual(){
        $dadata = new Dadata(Yii::$app->params['dadata_keys']['api_key'], Yii::$app->params['dadata_keys']['secret_key']);
        $dadata_company = $dadata->suggest('party', array("query" => $this->billing_company_inn));

        if (isset($dadata_company['suggestions']) && count($dadata_company['suggestions']) == 1) {
            $dadata_company = array_shift($dadata_company['suggestions']);
            if (isset($dadata_company['data']['state']['status']) && !empty($dadata_company['data']['state']['status'])) {
                switch ($dadata_company['data']['state']['status']) {
                    case 'ACTIVE':
                        $this->actual_company = 0;
                        break;
                    case 'LIQUIDATING':
                        $this->actual_company = 1;
                        break;
                    case 'LIQUIDATED':
                        $this->actual_company = 2;
                        break;
                    
                    default:
                        $this->actual_company = -1;
                        break;
                }
            }
        } else {
            $this->actual_company = -1;
        }

        return true;
    }

    public function loadPostcode(){
        $dadata = new Dadata(Yii::$app->params['dadata_keys']['api_key'], Yii::$app->params['dadata_keys']['secret_key']);
        $dadata_clean_address = $dadata->clean('address', $this->dadata_suggest_address);
        $dadata_clean_address = array_shift($dadata_clean_address);
        if (isset($dadata_clean_address['postal_code']) && !empty($dadata_clean_address['postal_code'])) {
            $this->postcode = $dadata_clean_address['postal_code'];
        } else {
            $this->postcode = '-1';
        }
    }

    public function autoRecycleCompany(){
        // загрузила инфу о компании по шd
        if($this->loadCompanyInfo()){
            if (strlen($this->billing_company_inn) == 10) {
                $dadata_clean_address = '';
                $dadata_company = '';
                // если из биллинга инфа пришла, то создаю экземпляр дадаты
                $dadata = new Dadata(Yii::$app->params['dadata_keys']['api_key'], Yii::$app->params['dadata_keys']['secret_key']);


                if (!empty($this->billing_company_inn)) {
                    // если ИНН не пустой, то отправляю запрос в подсказки
                    $dadata_company = $dadata->suggest('party', array("query" => $this->billing_company_inn)); 
                } elseif (!empty($this->billing_company_name)) {
                    // если ИНН пустой, то ищу в подсказках по имени компании
                    $dadata_company = $dadata->suggest('party', array("query" => $this->billing_company_name));
                } else {
                    // если всё пусто вдруг (хотя уже проверила), тогда проверка невозможна и дальше код выполнять нет смысла
                    $this->conclusion_address = 0;
                    $this->conclusion_company_name = 0;
                    return true;
                }



                if (isset($dadata_company['suggestions']) && count($dadata_company['suggestions']) > 1) {
                    // анализ если подсказки вернули несколько вариантов 

                    if (!empty($this->billing_company_address_jur)) {
                        $dadata_clean_address = $dadata->clean('address', $this->billing_company_address_jur);
                        $dadata_clean_address = array_shift($dadata_clean_address);
                        $this->dadata_clean_address = $dadata_clean_address['result'];
                    }

                    if (isset($dadata_clean_address) && !empty($dadata_clean_address)) {
                        foreach ($dadata_company['suggestions'] as $key => $company) {
                            if (empty($company['data']['address']['data'])) {
                                continue;
                            }

                            if ($company['data']['address']['data']['city'] == $dadata_clean_address['city'] 
                                && $company['data']['address']['data']['street'] == $dadata_clean_address['street']
                                && $company['data']['address']['data']['street_type_full'] == $dadata_clean_address['street_type_full']){
                                $suit_company['suggestions'][] = $company;
                                $dadata_company = $suit_company;
                                break;
                            }

                            if ($company['data']['address']['data']['street'] == $dadata_clean_address['street'] 
                                && $company['data']['address']['data']['street_type_full'] == $dadata_clean_address['street_type_full']
                                && $company['data']['address']['data']['region'] == $dadata_clean_address['region']) {
                                $suit_company['suggestions'][] = $company;
                                $dadata_company = $suit_company;
                                break;
                            }
                        }
                    }
                    if (isset($dadata_company['suggestions']) && count($dadata_company['suggestions']) > 1) {
                        $this->conclusion_address = 6;
                        $this->conclusion_company_name = 6;
                        return true;
                    }
                } 

                if (isset($dadata_company['suggestions']) && count($dadata_company['suggestions']) == 1) {
                    // анализ если подсказки вернули один вариант 
                    $dadata_company = array_shift($dadata_company['suggestions']);
                    $this->dadata_suggest_company_name = $dadata_company['value'];
                    $this->dadata_suggest_address = $dadata_company['data']['address']['value'];
                    $this->dadata_suggest_address_fias_id = $dadata_company['data']['address']['data']['fias_id'];

                    if (!empty($this->billing_company_address_jur)) {
                        //если есть с чем сравнивать из биллинга, то чистим через стандартизацию
                        if (empty($dadata_clean_address)) {
                            $dadata_clean_address = $dadata->clean('address', $this->billing_company_address_jur);
                            $dadata_clean_address = array_shift($dadata_clean_address);
                            $this->dadata_clean_address = $dadata_clean_address['result'];
                        }
                        if (isset($dadata_clean_address) && !empty($dadata_clean_address)) {
                            //если дадата смогла очистить адрес из ибллинга
                            $this->dadata_clean_address_fias_id = $dadata_clean_address['fias_id'];
                            if ((!empty($this->dadata_suggest_address_fias_id) && !empty($this->dadata_clean_address_fias_id)) && $this->dadata_suggest_address_fias_id == $this->dadata_clean_address_fias_id) {
                                $this->conclusion_address = 1;
                            } elseif (($dadata_clean_address['qc'] == 0 && $this->dadata_clean_address == $this->dadata_suggest_address) || $this->dadata_suggest_address == $this->billing_company_address_jur) {
                               $this->conclusion_address = 1;
                            } else {
                                if ((isset($dadata_clean_address['city']) && isset($dadata_company['data']['address']['data']['city']))
                                    && ($dadata_clean_address['city'] != $dadata_company['data']['address']['data']['city'])
                                ) {
                                    $this->conclusion_address = 5;
                                } else {
                                    $this->conclusion_address = 2;
                                }
                            }
                        } else {
                            $this->conclusion_address = 2;
                        }
                    } else {
                        $this->conclusion_address = 2;
                    }

                    if (!empty($this->billing_company_name) && !empty($this->dadata_suggest_company_name)) {
                        if (mb_strtoupper($this->billing_company_name) == mb_strtoupper($this->dadata_suggest_company_name)) {
                            $this->conclusion_company_name = 1;
                        } else {
                            $this->conclusion_company_name = 2;
                        }
                    } else {
                        $this->conclusion_company_name = 2;
                    }     
                } else {
                    // если подсказки ничего не вернули или при нескольких вариантах не удалось установить соответствие
                    $this->conclusion_address = 2;
                    $this->conclusion_company_name = 2;
                }

            
            } else {
                $this->conclusion_address = 7;
                $this->conclusion_company_name = 7;
            }
        } else {
            $this->conclusion_address = 0;
            $this->conclusion_company_name = 0;
        }

        return true;
    }
}
