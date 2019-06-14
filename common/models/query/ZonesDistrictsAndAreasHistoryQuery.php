<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\history\ZonesDistrictsAndAreasHistory]].
 *
 * @see \common\models\history\ZonesDistrictsAndAreasHistory
 */
class ZonesDistrictsAndAreasHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\history\ZonesDistrictsAndAreasHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\history\ZonesDistrictsAndAreasHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
