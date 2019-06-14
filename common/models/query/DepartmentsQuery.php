<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[Departments]].
 *
 * @see Departments
 */
class DepartmentsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Departments[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Departments|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
