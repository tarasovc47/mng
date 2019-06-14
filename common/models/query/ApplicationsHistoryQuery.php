<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[ApplicationsHistory]].
 *
 * @see ApplicationsHistory
 */
class ApplicationsHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ApplicationsHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ApplicationsHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
