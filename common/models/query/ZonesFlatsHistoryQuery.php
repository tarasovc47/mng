<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\history\ZonesFlatsHistory]].
 *
 * @see \common\models\history\ZonesFlatsHistory
 */
class ZonesFlatsHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\history\ZonesFlatsHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\history\ZonesFlatsHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
