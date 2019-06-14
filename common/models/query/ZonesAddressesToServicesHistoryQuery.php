<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\history\ZonesAddressesToServicesHistory]].
 *
 * @see \common\models\history\ZonesAddressesToServicesHistory
 */
class ZonesAddressesToServicesHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\history\ZonesAddressesToServicesHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\history\ZonesAddressesToServicesHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
