<?php

namespace common\models;

use Yii;
use common\models\history\DocsArchiveToConnectionTechnologiesHistory;
use yii\helpers\ArrayHelper;

class DocsArchiveToConnectionTechnologies extends \yii\db\ActiveRecord
{
    public $updater;
    public $updated_at;

    public static function tableName()
    {
        return 'docs_archive_to_connection_technologies';
    }

    public function rules()
    {
        return [
            [['doc_id', 'conn_tech_id', 'publication_status', 'created_at', 'cas_user_id'], 'required'],
            [['doc_id', 'conn_tech_id', 'publication_status', 'created_at', 'cas_user_id', 'updater', 'updated_at'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'doc_id' => 'Doc ID',
            'conn_tech_id' => 'Conn Tech ID',
            'publication_status' => 'Publication Status',
            'created_at' => 'Created At',
            'cas_user_id' => 'Cas User ID',
        ];
    }

    public function afterSave($insert, $changedAttributes){
        $history = new DocsArchiveToConnectionTechnologiesHistory();
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
        return new \common\models\query\DocsArchiveToConnectionTechnologiesQuery(get_called_class());
    }

    public function getConnTech()
    {
        return $this->hasOne(ConnectionTechnologies::className(), ['id' => 'conn_tech_id']);
    }

    public static function getConnTechsForDoc($doc_id, $publication_status = false)
    {
        $connection = Yii::$app->db;
        $where_status = '';
        if ($publication_status) {
            $where_status = ' AND publication_status = '.$publication_status;
        }
        $conn_techs = $connection
                            ->createCommand("SELECT id, conn_tech_id, publication_status FROM ".self::tableName()." WHERE doc_id = '".$doc_id."'".$where_status)
                            ->queryAll();

        return ArrayHelper::index($conn_techs, 'id');
    }
}
