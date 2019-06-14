<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use common\models\history\DocsTypesHistory;

class DocsTypes extends \yii\db\ActiveRecord
{
    public $updated_at;
    public $updater;
    public $sub_document_translate = [
        0 => 'Нет',
        1 => 'Да',
    ];
    public $available_for_translate = [
        0 => "Лицевой счёт",
        1 => "Сервис",
        2 => "Лицевой счёт и сервис",
    ];

    public static function tableName()
    {
        return 'docs_types';
    }

    public function rules()
    {
        return [
            [['available_for', 'sub_document', 'updater', 'updated_at'], 'integer'],
            [['name', 'available_for', 'folder', 'sub_document'], 'required'],
            [['name', 'folder'], 'string'],
            [['name', 'folder'], 'trim'],
            [['folder'], 'unique'],
            [['folder'], 'match', 'pattern' => '/^[a-zA-Z\_]+$/i', 'message' => 'Только латинские буквы и нижнее подчёркивание']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'available_for' => 'Доступен для привязки к',
            'folder' => 'Папка', 
            'sub_document' => 'Является подчинённым документом',
        ];
    }

    public static function find()
    {
        return new \common\models\query\DocsTypesQuery(get_called_class());
    }

    public function afterSave($insert, $changedAttributes){
        $history = new DocsTypesHistory();
        $history->setAttributes($this->getAttributes());
        $history->origin_id = $this->id;
        if (!$insert) {
           $history->created_at = $this->updated_at;
           $history->cas_user_id = $this->updater;
        }
        if ($insert) {
            $path = Yii::getAlias('@frontend/web/media/archive/docs_archive/').$this->folder;
            mkdir($path, 0775, true); 
        }
        $history->save();
    }

    public static function getDocTypesList($sub_document){
        $where = '';
        if ($sub_document != -1) {
            $where = " WHERE sub_document = '".$sub_document."'";
        }

        $connection = Yii::$app->db;
        $types = $connection
                            ->createCommand("
                                SELECT id, name
                                FROM docs_types"
                                .$where
                            )
                            ->queryAll();

        return ArrayHelper::map($types, 'id', 'name');
    }
}
