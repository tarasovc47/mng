<?php

namespace common\models;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use common\models\history\ApplicationsHistory;

class ApplicationsEvents extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'applications_events';
    }

    public function getApplication()
    {
        return $this->hasOne(Applications::className(), [
            'application_stack_id' => 'application_stack_id',
            "id_spec" => "application_id_spec"
        ]);
    }

    public function getApplicationHistory()
    {
        return $this->hasOne(ApplicationsHistory::className(), ["application_event_id" => "id"]);
    }

    public function getApplicationAttributes()
    {
        return $this->hasOne(ApplicationsAttributes::className(), ["application_event_id" => "id"]);
    }

    public function getApplicationProperties()
    {
        return $this->hasOne(ApplicationsProperties::className(), ["application_event_id" => "id"]);
    }

    public function getApplicationComment()
    {
        return $this->hasOne(ApplicationsComments::className(), ["application_event_id" => "id"]);
    }

    public function getCasUser()
    {
        return $this->hasOne(CasUser::className(), ['id' => 'cas_user_id']);
    }

    public function rules()
    {
        return [
            [['application_stack_id', 'application_id_spec', 'type', 'created_at', 'cas_user_id'], 'required'],
            [['application_stack_id'], 'string'],
            [['application_stack_id'], 'trim'],
            [['type', 'created_at', 'cas_user_id', 'application_id_spec'], 'integer'],
            [['vars'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'application_stack_id' => 'Application Stack ID',
            'application_id_spec' => 'Application ID Spec',
            'type' => 'Тип',
            'cas_user_id' => 'Пользователь',
            'vars' => 'Дополнительные данные',
        ];
    }

    public static function find()
    {
        return new \common\models\query\ApplicationsEventsQuery(get_called_class());
    }

    /** За красивое отображение статусов отвечает common\widgets\ApplicationsEvents */
    public static function getTypes($value = -1){
        $statuses = array(
            1 => "Создание",
            2 => "Принятие в работу",
            3 => "Назначение отвественного",
            4 => "Передача в другой отдел",
        );

        if($value > -1)
            return $statuses[$value];

        return $statuses;
    }

    public function afterSave($insert, $changedAttributes){
        parent::afterSave($insert, $changedAttributes);
 
        if($insert){
            $application_history = new ApplicationsHistory();
            $application_history->setAttributes($this->application->getAttributes());
            $application_history->application_event_id = $this->id;
            $application_history->save();
        }
    }
}