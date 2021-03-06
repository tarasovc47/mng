<?php

namespace common\models;

use Yii;
use common\models\history\DocsArchiveToServicesHistory;
use yii\helpers\ArrayHelper;

class DocsArchiveToServices extends \yii\db\ActiveRecord
{
    public $updater;
    public $updated_at;

    public static function tableName()
    {
        return 'docs_archive_to_services';
    }

    public function rules()
    {
        return [
            [['doc_id', 'service_id', 'publication_status', 'created_at', 'cas_user_id'], 'required'],
            [['doc_id', 'service_id', 'publication_status', 'created_at', 'cas_user_id', 'updater', 'updated_at'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'doc_id' => 'Doc ID',
            'service_id' => 'Service ID',
            'publication_status' => 'Publication Status',
            'created_at' => 'Created At',
            'cas_user_id' => 'Cas User ID',
        ];
    }
    
    public function afterSave($insert, $changedAttributes){
        $history = new DocsArchiveToServicesHistory();
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
        return new \common\models\query\DocsArchiveToServicesQuery(get_called_class());
    }

    public static function getServicesForDoc($doc_id, $publication_status = false)
    {
        $connection = Yii::$app->db;
        $where_status = '';
        if ($publication_status) {
            $where_status = ' AND publication_status = '.$publication_status;
        }
        $services = $connection
                            ->createCommand("SELECT id, service_id, publication_status FROM ".self::tableName()." WHERE doc_id = '".$doc_id."'".$where_status)
                            ->queryAll();

        return ArrayHelper::index($services, 'id');
    }
}
