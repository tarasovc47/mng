<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[ManagCompaniesBranches]].
 *
 * @see ManagCompaniesBranches
 */
class ManagCompaniesBranchesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ManagCompaniesBranches[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ManagCompaniesBranches|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
