<?php

namespace common\models\history;

use Yii;
use common\models\query\ApplicationsHistoryQuery;

class ApplicationsHistory extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'applications_history';
    }

    public function getApplicationsEvents()
    {
        return $this->hasOne(ApplicationsEvents::className(), ["id" => "application_history_id"]);
    }

    public function rules()
    {
        return [
            [['application_stack_id', 'id_spec', 'loki_basic_service_id', 'application_type_id', 'department_id', 'responsible', 'application_status_id', 'application_event_id', 'connection_technology_id'], 'required'],
            [['application_stack_id'], 'string'],
            [['application_stack_id'], 'trim'],
            [['id_spec', 'loki_basic_service_id', 'application_type_id', 'department_id', 'responsible', 'application_status_id', 'application_event_id', 'connection_technology_id', 'group_id'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'application_stack_id' => 'Application Stack ID',
            'id_spec' => 'Id Spec',
            'loki_basic_service_id' => 'Loki Basic Service ID',
            'application_type_id' => 'Application Type ID',
            'department_id' => 'Department ID',
            'responsible' => 'Responsible',
            'application_status_id' => 'Application Status ID',
            'application_event_id' => 'Application Event ID',
        ];
    }

    public static function find()
    {
        return new ApplicationsHistoryQuery(get_called_class());
    }
}
