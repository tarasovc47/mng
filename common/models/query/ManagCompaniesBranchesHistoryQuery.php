<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\history\ManagCompaniesBranchesHistory]].
 *
 * @see \common\models\history\ManagCompaniesBranchesHistory
 */
class ManagCompaniesBranchesHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\history\ManagCompaniesBranchesHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\history\ManagCompaniesBranchesHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
