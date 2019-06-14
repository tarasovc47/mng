<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[ApplicationsStatuses]].
 *
 * @see ApplicationsStatuses
 */
class ApplicationsStatusesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ApplicationsStatuses[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ApplicationsStatuses|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
