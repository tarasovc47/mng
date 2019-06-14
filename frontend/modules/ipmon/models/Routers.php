<?php
namespace frontend\modules\ipmon\models;
use yii\db\ActiveRecord;

class Routers extends ActiveRecord  ///класс обзывается также как и таблица в БД
//class Routers extends ActiveRecord  ///класс обзывается также как и таблица в БД, обращается
{
    public static function tableName()
    {
        return 'routers'; //возвращает имя таблицы, с которой нужно работать
    }
}