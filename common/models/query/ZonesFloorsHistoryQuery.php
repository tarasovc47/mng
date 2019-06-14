<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\history\ZonesFloorsHistory]].
 *
 * @see \common\models\history\ZonesFloorsHistory
 */
class ZonesFloorsHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\history\ZonesFloorsHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\history\ZonesFloorsHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
