<?php
 
namespace common\models;

use Yii;
use yii\db\Query;
use yii\helpers\Json;
use common\components\SiteHelper;
use yii\helpers\ArrayHelper;

class Applications extends \yii\db\ActiveRecord
{
    private $id;

    public function getId()
    {
        return $this->application_stack_id . "-" . $this->id_spec;
    }

    public static function primaryKey()
    {
        return ['application_stack_id', 'id_spec'];
    }

    public static function tableName()
    {
        return 'applications';
    }

    public function getApplicationsStack()
    {
        return $this->hasOne(ApplicationsStacks::className(), ['id' => 'application_stack_id']);
    }

    public function getApplicationsEvents()
    {
        return $this->hasMany(ApplicationsEvents::className(), [
            'application_stack_id' => 'application_stack_id',
            'application_id_spec' => 'id_spec'
        ])->orderBy([ "created_at" => SORT_ASC ]);
    }

    public function getApplicationsStatus()
    {
        return $this->hasOne(ApplicationsStatuses::className(), ['id' => 'application_status_id']);
    }

    public function getApplicationsType()
    {
        return $this->hasOne(ApplicationsTypes::className(), ['id' => 'application_type_id']);
    }

    public function getResponsibleUser()
    {
        return $this->hasOne(CasUser::className(), ['id' => 'responsible']);
    }

    public function getDepartment()
    {
        return $this->hasOne(Departments::className(), ['id' => 'department_id']);
    }

    public function getConnectionTechnology()
    {
        return $this->hasOne(ConnectionTechnologies::className(), ['id' => 'connection_technology_id']);
    }

    public function rules()
    {
        return [
            [['id_spec', 'loki_basic_service_id', 'application_type_id', 'department_id', 'connection_technology_id', 'responsible', 'application_status_id', 'group_id'], 'integer'],
            [['application_stack_id', 'id_spec', 'loki_basic_service_id', 'application_type_id', 'connection_technology_id'], 'required'],
            [['department_id'], 'required', 'message' => 'Выберите отдел, в которой отправится заявка.'],
            [['application_stack_id'], 'string', 'max' => 255],
            [['application_stack_id'], 'trim'],
            [['connection_technology_id'], 'validateConnectionTechnology'],
            [['department_id'], 'validateDepartment'],
            [['group_id'], 'validateGroup'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'application_stack_id' => 'ID пула заявок',
            'id_spec' => 'Дополнительный ID',
            'loki_basic_service_id' => 'Loki Basic Service ID',
            'attributes' => 'Атрибуты',
            'type' => 'Тип',
            'department_id' => 'Отдел компании',
            'scenario_id' => 'Сценарий создания',
        ];
    }

    public static function find()
    {
        return new \common\models\query\ApplicationsQuery(get_called_class());
    }

    public function validateConnectionTechnology($attribute, $params)
    {
        if($this->$attribute == 0){
            $this->addError($attribute, 'Обязательно нужно выбрать технологию подключения.');
        }
        else{
            $connection_technology = ConnectionTechnologies::findOne($this->$attribute);
            if(!$connection_technology){
                $this->addError($attribute, 'Ошибка определения технологии подключения. Перезагрузите страницу и попробуйте снова.');
            }
        }
    }

    public function validateDepartment($attribute, $params)
    {
        $department = Departments::findOne($this->$attribute);
        if(!$department){
            $this->addError($attribute, 'Ошибка определения отдела. Перезагрузите страницу и попробуйте снова.');
        }
    }

    public function validateGroup($attribute, $params)
    {
        if($this->department_id == 2){
            if(empty($this->$attribute) || ($this->$attribute == 0)){
                $this->addError($attribute, 'Выберите бригаду, в которую отправится заявка.');
            }
            else{
                $group = UsersGroups::findOne($this->$attribute);
                if(!$group || ($group->department_id != 2)){
                    $this->addError($attribute, 'Ошибка определения бригады. Перезагрузите страницу и попробуйте снова.');
                }
            }
        }
    }

    public function prepareErrors(){
        $response = [];
        $errors = $this->getErrors();

        foreach($errors as $attribute => $messages){
            foreach($messages as $message){
                $response[] = $message;
            }
        }

        return $response;
    }

    public function getFirstTakenEvent(){
        foreach($this->applicationsEvents as $event){
            if($event->type == 2){
                return $event;
            }
        }

        return false;
    }

    public function getFirstSetResponsibleEvent(){
        foreach($this->applicationsEvents as $event){
            if($event->type == 3){
                return $event;
            }
        }

        return false;
    }

    public function setTakenStatus($cas_user_id){
        $taken = 2;

        $this->application_status_id = $taken;
        $this->save();

        $application_event = new ApplicationsEvents();
        $application_event->application_stack_id = $this->application_stack_id;
        $application_event->application_id_spec = $this->id_spec;
        $application_event->created_at = time();
        $application_event->type = 2;
        $application_event->cas_user_id = $cas_user_id;
        $application_event->scenario_id = 0;
        $application_event->save();

        return $application_event;
    }

    public function setResponsible($responsible_cas_user_id, $init_cas_user_id){
        $this->responsible = $responsible_cas_user_id;
        $this->save();

        $vars = [
            'responsible' => $responsible_cas_user_id,
        ];

        $application_event = new ApplicationsEvents();
        $application_event->application_stack_id = $this->application_stack_id;
        $application_event->application_id_spec = $this->id_spec;
        $application_event->created_at = time();
        $application_event->type = 3;
        $application_event->cas_user_id = $init_cas_user_id;
        $application_event->scenario_id = 0;
        $application_event->vars = serialize($vars);
        $application_event->save();

        return $application_event;
    }

    public function setDepartment($department_id, $init_cas_user_id, $group_id){
        $this->department_id = $department_id;
        $this->application_status_id = 6;
        $this->responsible = 0;
        $this->group_id = $group_id;
        $this->save();

        $vars = [
            'department_id' => $department_id,
        ];

        $application_event = new ApplicationsEvents();
        $application_event->application_stack_id = $this->application_stack_id;
        $application_event->application_id_spec = $this->id_spec;
        $application_event->created_at = time();
        $application_event->type = 4;
        $application_event->cas_user_id = $init_cas_user_id;
        $application_event->scenario_id = 0;
        $application_event->vars = serialize($vars);
        $application_event->save();

        return $application_event;
    }

    public function refuse($init_cas_user_id){
        $this->responsible = 0;
        $this->save();

        $application_event = new ApplicationsEvents();
        $application_event->application_stack_id = $this->application_stack_id;
        $application_event->application_id_spec = $this->id_spec;
        $application_event->created_at = time();
        $application_event->type = 5;
        $application_event->cas_user_id = $init_cas_user_id;
        $application_event->scenario_id = 0;
        $application_event->save();

        return $application_event;
    }

    public function setStatus($status_id, $init_cas_user_id){
        $this->application_status_id = $status_id;
        $this->save();

        $vars = [
            'status_id' => $status_id,
        ];

        $application_event = new ApplicationsEvents();
        $application_event->application_stack_id = $this->application_stack_id;
        $application_event->application_id_spec = $this->id_spec;
        $application_event->created_at = time();
        $application_event->type = 6;
        $application_event->cas_user_id = $init_cas_user_id;
        $application_event->scenario_id = 0;
        $application_event->vars = serialize($vars);
        $application_event->save();

        return $application_event;
    }

    public function complete($cas_user){
        $this->application_status_id = 7;

        // if($cas_user->department->id == 2){
            $this->department_id = 1;
            $this->responsible = 0;
            $this->group_id = 0;
        // }

        $this->save();

        $application_event = new ApplicationsEvents();
        $application_event->application_stack_id = $this->application_stack_id;
        $application_event->application_id_spec = $this->id_spec;
        $application_event->created_at = time();
        $application_event->type = 7;
        $application_event->cas_user_id = $cas_user->id;
        $application_event->scenario_id = 0;
        $application_event->save();

        return $application_event;
    }

    public function revision($department_id, $group_id, $init_cas_user_id){
        $this->application_status_id = 6;
        $this->department_id = $department_id;
        $this->group_id = $group_id;
        $this->save();

        $vars = [
            'department_id' => $department_id,
        ];

        $application_event = new ApplicationsEvents();
        $application_event->application_stack_id = $this->application_stack_id;
        $application_event->application_id_spec = $this->id_spec;
        $application_event->created_at = time();
        $application_event->type = 8;
        $application_event->cas_user_id = $init_cas_user_id;
        $application_event->scenario_id = 0;
        $application_event->vars = serialize($vars);
        $application_event->save();

        return $application_event;
    }
    
    public function close($init_cas_user_id){
        $this->application_status_id = 8;
        $this->save();

        $application_event = new ApplicationsEvents();
        $application_event->application_stack_id = $this->application_stack_id;
        $application_event->application_id_spec = $this->id_spec;
        $application_event->created_at = time();
        $application_event->type = 9;
        $application_event->cas_user_id = $init_cas_user_id;
        $application_event->scenario_id = 0;
        $application_event->save();

        return $application_event;
    }

    public function findForRevision(){
        $response = [
            'department_id' => 0,
            'group_id' => 0,
        ];

        $events = $this->applicationsEvents;
        $events = array_reverse($events);

        $check = false;
        foreach($events as $event){
            if($check){
                $response['department_id'] = $event->applicationHistory->department_id;
                $response['group_id'] = $event->applicationHistory->group_id;
                break;
            }

            if($event->type == 7){
                $check = true;
            }
        }

        return $response;
    }

    public static function identifyBrigade($loki_basic_service_id){
        $uuid = Yii::$app->db_billing
            ->createCommand("SELECT kladr_to_place(address_kladr)
                FROM loki_basic_service  
                WHERE id = '" . $loki_basic_service_id . "'")
            ->queryScalar();

        $address = ZonesAddresses::findOne([ 'address_uuid' => $uuid ]);

        if(empty($address)){
            $data = SiteHelper::loadAllAddressInfoByUuid($uuid);

            if(isset($data['result'][0]['fias'])){
                $fias_levels = array_reverse($data['result'][0]['fias']);

                foreach($fias_levels as $key => $level){
                    $uuid = SiteHelper::loadAllAddressInfoByFias($level);
                    if (isset($uuid['result'][0]['uuid'])) {
                        $address = ZonesAddresses::find()->where(['address_uuid' => $uuid['result'][0]['uuid']])->with('area', 'area.usersGroup')->one();
                        if(!empty($address)){
                            break;
                        }
                    }
                }
            }
        }

        return (!empty($address) && isset($address->area)) ? $address->area->usersGroup->id : 0;
    }

    public static function loadApplication($application_id){
        if(!is_array($application_id)){
            $application_id = explode("-", $application_id);
        }

        $application = Applications::find()
                ->where([ "application_stack_id" => $application_id[0], "id_spec" => $application_id[1] ])
                ->one();

        return $application;
    }

    public static function socketValidation($param, $value){
        $messages = [];
        $variables = [];

        if($param == "application_id"){
            if(empty($value)){
                $messages[] = "Возникла непредвиденная ошибка. Не передан идентификатор заявки.";
            }
            else{
                $application = self::loadApplication($value);

                if(!$application){
                    $messages[] = "Возникла непредвиденная ошибка. Не удалось определить заявку.";
                }
                else{
                    $variables["application"] = $application;
                }
            }
        }

        if($param == "responsible"){
            if(empty($value)){
                $messages[] = "Необходимо выбрать ответственного.";
            }
            else{
                $cas_user = CasUser::findOne($value);

                if(!$cas_user){
                    $messages[] = "Возникла непредвиденная ошибка. Не удалось определить пользователя.";
                }
                else{
                    $variables["responsible"] = $cas_user;
                }
            }
        }

        if($param == "department_id"){
            if(empty($value)){
                $messages[] = "Необходимо выбрать отдел.";
            }
            else{
                $department = Departments::findOne($value);

                if(!$department){
                    $messages[] = "Возникла непредвиденная ошибка. Не удалось определить отдел.";
                }
                else{
                    $variables["department"] = $department;
                }
            }
        }

        if($param == "status_id"){
            if(empty($value)){
                $messages[] = "Необходимо выбрать статус.";
            }
            else{
                $status = ApplicationsStatuses::findOne($value);

                if(!$status){
                    $messages[] = "Возникла непредвиденная ошибка. Не удалось определить статус.";
                }
                else{
                    $variables["status"] = $status;
                }
            }
        }

        if($param == "group_id"){
            if(empty($value)){
                $messages[] = "Необходимо выбрать бригаду.";
            }
            else{
                $group = UsersGroups::findOne($value);

                if(!$group || ($group->department_id != 2)){
                    $messages[] = "Ошибка определения бригады. Перезагрузите страницу и попробуйте снова.";
                }
                else{
                    $variables["group"] = $group;
                }
            }
        }

        if($param == "comment"){
            if(!empty($value)){
                $application_comment = new ApplicationsComments();
                $application_comment->application_event_id = 0;
                $application_comment->comment = $value;

                if(!$application_comment->validate()){
                    $errors = $application_comment->prepareErrors();
                    $messages = ArrayHelper::merge($messages, $errors);
                }
                else{
                    $variables["application_comment"] = $application_comment;
                }
            }
            else{
                $messages[] = "Необходимо написать комментарий";
            }
        }

        if($param == "attributes"){
            if(!empty($value)){
                $application_attributes = new ApplicationsAttributes();
                $application_attributes->application_event_id = 0;
                $application_attributes->attributes = $value;

                if(!$application_attributes->validate()){
                    $errors = $application_attributes->prepareErrors();
                    $messages = ArrayHelper::merge($messages, $errors);
                }
                else{
                    $variables["application_attributes"] = $application_attributes;
                }
            }
            else{
                $messages[] = "Необходимо отметить атрибуты.";
            }
        }

        if($param == "properties"){
            if(!empty($value)){
                $application_properties = new ApplicationsProperties();
                $application_properties->application_event_id = 0;
                $application_properties->properties = $value;

                if(!$application_properties->validate()){
                    $errors = $application_properties->prepareErrors();
                    $messages = ArrayHelper::merge($messages, $errors);
                }
                else{
                    $variables["application_properties"] = $application_properties;
                }
            }
            else{
                $messages[] = "Необходимо отметить атрибуты.";
            }
        }

        $response["messages"] = $messages;
        $response["variables"] = $variables;

        return $response;
    }
}