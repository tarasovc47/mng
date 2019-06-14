<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[ApplicationsTypes]].
 *
 * @see ApplicationsTypes
 */
class ApplicationsTypesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ApplicationsTypes[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ApplicationsTypes|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
