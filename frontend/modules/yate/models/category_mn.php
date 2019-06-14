<?php
namespace frontend\modules\yate\models;
use Yii;
class category_mn extends \yii\db\ActiveRecord
{
    public static function getDb() {
        return Yii::$app->get('db_yate');
    }
    public static function tableName()
    {
        return 'category_mn';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
//            'id' => 'ID',
//            'ip' => 'IP адрес',
//            'mac' => 'MAC адрес',
//            'vlan' => 'VLAN',
//            'description' => 'Описание',
//            'ipmon_id' => 'ID из ipmon',
//            'active' => 'Статический IP',
//            'mount_date' => 'Замонтирован',
//            'umount_date' => 'Umount Date',
//            'sw_model' => 'Модель',
//            'configured' => 'Сконфигурировать',
        ];
    }
}