<?php

namespace common\models;

use \Datetime;
use Yii;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use yii\db\ActiveRecord;
use common\models\DocsTypes;
use common\models\history\DocsArchiveHistory;
use common\components\SiteHelper;
use common\models\DocsArchiveToLokiBasicServices as LBS;
use common\models\DocsArchiveToServices;
use common\models\DocsArchiveToConnectionTechnologies as CT;

class DocsArchive extends \yii\db\ActiveRecord
{
    public $file;
    public $loki_basic_service_ids;
    public $service_types;
    public $conn_techs;
    public $updater;
    public $updated_at;

    public static function tableName()
    {
        return 'docs_archive';
    }

    public function rules()
    {
        return [
            [['doc_type_id', 'abonent', 'cas_user_id', 'created_at', 'opened_at', 'parent_id', 'billing_contract_date', 'updater', 'updated_at', 'publication_status'], 'integer'],
            [['name', 'doc_type_id', 'created_at', 'client_id', 'cas_user_id', 'label', 'opened_at', 'publication_status'], 'required'],
            [['file'], 'required', 'on' => 'create'],
            [['name', 'label', 'descr', 'client_id', 'extension', 'billing_contract_id', 'billing_contract_name', 'billing_contract_type'], 'string'],
            [['name', 'label', 'descr', 'client_id', 'extension', 'billing_contract_id', 'billing_contract_name', 'billing_contract_type'], 'trim'],
            [['file'], 'file', 'extensions' => 'png, jpg, jpeg, xls, pdf, doc, docx, xslx', 'maxSize' => 20000000],
            [['loki_basic_service_ids', 'service_types', 'conn_techs'], 'each', 'rule' => ['integer']],
        ];
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Основной документ',
            'name' => 'Имя файла',
            'doc_type_id' => 'Тип документа',
            'label' => 'Номер документа',
            'created_at' => 'Дата создания',
            'descr' => 'Комментарий',
            'abonent' => 'Абонент',
            'client_id' => 'Лицевой счёт',
            'loki_basic_service_ids' => 'Сервисы',
            'cas_user_id' => 'Добавил',
            'file' => 'Загрузить документ', 
            'extension' => 'Расширение',
            'opened_at' => 'Дата подписания документа',
            'billing_contract_id' => 'Документ в биллинге',
            'billing_contract_name' => 'Номер документа в биллинге',
            'billing_contract_type' => 'Тип документа в биллинге',
            'billing_contract_date' => 'Дата документа в биллинге',
            'service_types' => 'Типы сервисов',
            'conn_techs' => 'Технологии подключения',
            'publication_status' => 'Статус публикации',
        ];
    }

    public function afterSave($insert, $changedAttributes){
        $this->processingRelations($this->loki_basic_service_ids, LBS::className(), 'getServicesForDoc', 'loki_basic_service_id');
        $this->processingRelations($this->service_types, DocsArchiveToServices::className(), 'getServicesForDoc', 'service_id');
        $this->processingRelations($this->conn_techs, CT::className(), 'getConnTechsForDoc', 'conn_tech_id');

        $history = new DocsArchiveHistory();
        $history->setAttributes($this->getAttributes());
        $history->origin_id = $this->id;
        if (!$insert) {
           $history->created_at = $this->updated_at;
           $history->cas_user_id = $this->updater;
        }
        $history->save();
    }

    public static function find()
    {
        return new \common\models\query\DocsArchiveQuery(get_called_class());
    }

    // Настройка связей с другими таблицами
    public function getLokiBasicServiceIds()
    {
        return $this->hasMany(LBS::className(), ['doc_id' => 'id'])->
       andWhere(['publication_status' => 1]);
    }

    public function getServiceTypes()
    {
        return $this->hasMany(Services::className(), ['id' => 'service_id'])
                    ->viaTable(DocsArchiveToServices::tableName(), ['doc_id' => 'id'], 
                        function($query) {
                        $query->where(['publication_status' => 1]);
                    });
    }

    public function getConnTechs()
    {
        return $this->hasMany(ConnectionTechnologies::className(), ['id' => 'conn_tech_id'])
                    ->viaTable(CT::tableName(), ['doc_id' => 'id'], 
                        function($query) {
                        $query->where(['publication_status' => 1]);
                    });
    }

    public function getExtraData(){
        $this->loki_basic_service_ids = ArrayHelper::map(LBS::getServicesForDoc($this->id, 1), 'id', 'loki_basic_service_id');
        $this->service_types = ArrayHelper::map(DocsArchiveToServices::getServicesForDoc($this->id, 1), 'id', 'service_id');
        $this->conn_techs = ArrayHelper::map(CT::getConnTechsForDoc($this->id, 1), 'id', 'conn_tech_id');
    }

    public function uploadFile(){
        $folder = DocsTypes::findOne($this->doc_type_id);
        $dir = '/media/archive/docs_archive/'.$folder->folder;
        //$dir = Yii::getAlias('@frontend/web/media/archive/docs_archive/').$folder->folder;
        $fileName = SiteHelper::genUniqueKey(30). '.' . $this->file->extension;
        while (file_exists($dir."/".$fileName)) {
            $fileName = SiteHelper::genUniqueKey(30). '.' . $this->file->extension;
        }
        $this->extension = $this->file->extension;
        $this->file->saveAs($dir."/".$fileName);
        $this->file = $fileName; // без этого ошибка
        $this->name = "/media/archive/docs_archive/".$folder->folder."/".$fileName;
    }

    protected function processingRelations($data, $model_name, $method_name, $column_name)
    {
        $old_data = $model_name::$method_name($this->id);
        $old_data_map = ArrayHelper::map($old_data, 'id', $column_name);

        if (!empty($data)) {
            if (is_array($data)) {
                foreach ($data as $key => $value) {
                    if (in_array($value, $old_data)) {
                        unset($old_data[array_search($value, $old_data)]);
                        $model = $model_name::findOne([$column_name => $value, 'doc_id' => $this->id]);
                        $model->publication_status = 1;
                        $model->updated_at = $this->updated_at;
                        $model->updater = $this->updater;
                        $model->save();
                    } else {
                        $model = new $model_name();
                        $model->doc_id = $this->id;
                        $model->$column_name = $value;
                        $model->publication_status = 1;
                        $model->created_at = $this->created_at;
                        $model->cas_user_id = $this->cas_user_id;
                        $model->save();
                    }
                }
            } else {
                if (in_array($data, $old_data)) {
                    unset($old_data[array_search($data, $old_data)]);
                    $model = $model_name::findOne([$column_name => $data, 'doc_id' => $this->id]);
                    $model->publication_status = 1;
                    $model->updated_at = $this->updated_at;
                    $model->updater = $this->updater;
                    $model->save();
                } else {
                    $model = new $model_name();
                    $model->doc_id = $this->id;
                    $model->$column_name = $data;
                    $model->publication_status = 1;
                    $model->created_at = $this->created_at;
                    $model->cas_user_id = $this->cas_user_id;
                    $model->save();
                }
            }
        }

        if (!empty($old_data)) {
            foreach ($old_data as $key => $value) {
                $model = $model_name::findOne([$column_name => $value, 'doc_id' => $this->id]);
                $model->publication_status = 0;
                $model->updated_at = $this->updated_at;
                $model->updater = $this->updater;
                $model->save();
            }
        }
    }

    public static function getClientIDs($abonent){
        $client_ids = Yii::$app->db_billing
                            ->createCommand("SELECT client_id FROM base_clients WHERE abonent = '".$abonent."'")
                            ->queryAll();

        return $client_ids = ArrayHelper::map($client_ids, 'client_id', 'client_id');
    }

    public static function getLokiBasicServiceIDsList($client_id){
        $user_ids = Yii::$app->db_billing
                            ->createCommand("SELECT lbs.id as loki_basic_service_id, lbs.user_id, lbs.date_create, lbs.date_expire
                                FROM loki_basic_service lbs 
                                LEFT JOIN base_clients bc ON lbs.base_client_id = bc.id
                                WHERE bc.client_id = '".$client_id."'")
                            ->queryAll();

        foreach ($user_ids as $key => $id) {
            if (!empty($id['date_create'])) {
                $user_ids[$key]['user_id'] .= " (".date('d.m.Y' ,strtotime($id['date_create']));
                if (!empty($id['date_expire'])) {
                    $user_ids[$key]['user_id'] .= " - ".date('d.m.Y' ,strtotime($id['date_expire']));
                }
                $user_ids[$key]['user_id'] .= ')';
            }
        }

        $user_ids = ArrayHelper::map($user_ids, 'loki_basic_service_id', 'user_id');
        return $user_ids;
    }

    public static function getOneLokiBasicServiceId($id){
        $user_id = Yii::$app->db_billing
                        ->createCommand("SELECT user_id, date_create, date_expire
                            FROM loki_basic_service WHERE id = '".$id."'")
                        ->queryOne();

        if (!empty($user_id['date_create'])) {
            $user_id['user_id'] .= " (".date('d.m.Y' ,strtotime($user_id['date_create']));
            if (!empty($user_id['date_expire'])) {
                $user_id['user_id'] .= " - ".date('d.m.Y' ,strtotime($user_id['date_expire']));
            }
            $user_id['user_id'] .= ')';
        }
        return $user_id['user_id'];
    }

    public static function getOneCasUser($id){
        $cas_user = Yii::$app->db
                            ->createCommand("SELECT concat_ws(' ', first_name, last_name) as name 
                                FROM cas_user 
                                WHERE id = '{$id}'")
                            ->queryOne();

        return $cas_user['name'];
    }

    public static function getClientContractsList($client_id){
        $pre_contracts = Yii::$app->db_billing
                            ->createCommand("SELECT cc.contract_date as date, cc.contract_number as number, cc.contract_id, cct.descr as type
                                FROM contract_contracts cc
                                LEFT JOIN contract_contract_types cct ON cc.contract_type = cct.contract_type
                                WHERE client_id = '{$client_id}'")
                            ->queryAll();

        foreach ($pre_contracts as $key => $contract) {
            $pre_contracts[$key]['for_list'] = $contract['number'].' от '.$contract['date'].' ('.$contract['type'].')';
        }

        $contracts = ArrayHelper::map($pre_contracts, 'contract_id', 'for_list');

        return $contracts;

    }

    public static function getOneContract($contract_id){
        $contract = Yii::$app->db_billing
                            ->createCommand("
                                SELECT cc.contract_date as date, cc.contract_number as number, cc.contract_id, cct.descr as type
                                FROM contract_contracts cc
                                LEFT JOIN contract_contract_types cct ON cc.contract_type = cct.contract_type
                                WHERE contract_id = '{$contract_id}'
                            ")
                            ->queryOne();

        $contract['date'] = strtotime($contract['date']);
        $contract['opened_at'] = date('d-m-Y', $contract['date']);
        return $contract;

    }

    public static function getStatistics(){
        $statistics = array();
        // сколько всего доков
        $statistics['all'] = Yii::$app->db
                            ->createCommand("
                                SELECT count(*) 
                                FROM docs_archive
                            ")
                            ->queryScalar();

        //сколько доков за текущий месяц
        $current_month_start = (new DateTime('first day of this month midnight'))->getTimestamp();
        $statistics['all_current_month'] = Yii::$app->db
                            ->createCommand("
                                SELECT count(*) 
                                FROM docs_archive
                                WHERE created_at >= " . $current_month_start
                            )
                            ->queryScalar();  

        //сколько за текущий месяц по каждому юзеру
        $statistics['users_current_month'] = Yii::$app->db
                            ->createCommand("
                                SELECT cas_user_id, array_length(array_agg(id), 1) as count
                                FROM docs_archive
                                WHERE created_at >= {$current_month_start}
                                GROUP BY cas_user_id
                            ")
                            ->queryAll();                  

        return $statistics;

    }
}
