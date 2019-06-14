<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[ManagCompany]].
 *
 * @see ManagCompany
 */
class ManagCompaniesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ManagCompany[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ManagCompany|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
