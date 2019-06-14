<?php

namespace common\models;

use Yii;
use common\models\history\TariffsGroupsHistory;

class TariffsGroups extends \yii\db\ActiveRecord
{
    public $updated_at;
    public $updater;
    public $tariffs;

    public static function tableName()
    {
        return 'tariffs_groups';
    }

    public function rules()
    {
        return [
            [['name', 'publication_status', 'created_at', 'cas_user_id', 'abonent_type', 'tariffs'], 'required'],
            [['publication_status', 'created_at', 'cas_user_id', 'updater', 'updated_at', 'abonent_type'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['comment'], 'string'],
            [['name', 'comment'], 'trim'],
            [['tariffs'], 'each', 'rule' => ['integer']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'abonent_type' => 'Тип абонента',
            'tariffs' => 'Тарифные планы',
            'comment' => 'Комментарий',
            'publication_status' => 'Publication Status',
            'created_at' => 'Created At',
            'cas_user_id' => 'Cas User ID',
        ];
    }

    public static function find()
    {
        return new \common\models\query\TariffsGroupsQuery(get_called_class());
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->processingRelations($this->tariffs, TariffsToGroups::className(), 'loadTariffsListForGroup', 'tariff_id');

        $history = new TariffsGroupsHistory();
        $history->setAttributes($this->getAttributes());
        $history->origin_id = $this->id;
        if (!$insert) {
           $history->created_at = $this->updated_at;
           $history->cas_user_id = $this->updater;
        }
        $history->save();

        parent::afterSave($insert, $changedAttributes);
    }

    public function getTariffsToGroups()
    {
        return $this->hasMany(TariffsToGroups::className(), ['tariffs_group_id' => 'id'])->
       andWhere(['publication_status' => 1]);
    }

    protected function processingRelations($data, $model_name, $method_name, $column_name)
    {
        $old_data = $model_name::$method_name($this->id);

        if (!empty($data)) {
            if (is_array($data)) {
                foreach ($data as $key => $value) {
                    if (in_array($value, $old_data)) {
                        unset($old_data[array_search($value, $old_data)]);
                        $model = $model_name::findOne([$column_name => $value, 'tariffs_group_id' => $this->id]);
                        $model->publication_status = 1;
                        $model->updated_at = $this->updated_at;
                        $model->updater = $this->updater;
                        $model->save();
                    } else {
                        $model = new $model_name();
                        $model->tariffs_group_id = $this->id;
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
                    $model = $model_name::findOne([$column_name => $data, 'tariffs_group_id' => $this->id]);
                    $model->publication_status = 1;
                    $model->updated_at = $this->updated_at;
                    $model->updater = $this->updater;
                    $model->save();
                } else {
                    $model = new $model_name();
                    $model->tariffs_group_id = $this->id;
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
                $model = $model_name::findOne([$column_name => $value, 'tariffs_group_id' => $this->id]);
                $model->publication_status = 0;
                $model->updated_at = $this->updated_at;
                $model->updater = $this->updater;
                $model->save();
            }
        }
    }

    // получения значений для алреса из всех связанных таблиц при редактировании
    public function loadRelatedValues()
    {
        $this->tariffs = TariffsToGroups::loadTariffsListForGroup($this->id, 1);

        return true;
    }
}
