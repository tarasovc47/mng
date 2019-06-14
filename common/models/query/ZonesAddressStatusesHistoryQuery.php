<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\history\ZonesAddressStatusesHistory]].
 *
 * @see \common\models\history\ZonesAddressStatusesHistory
 */
class ZonesAddressStatusesHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\history\ZonesAddressStatusesHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\history\ZonesAddressStatusesHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
