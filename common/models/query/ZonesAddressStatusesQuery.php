<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[ZonesAddressStatuses]].
 *
 * @see ZonesAddressStatuses
 */
class ZonesAddressStatusesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ZonesAddressStatuses[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ZonesAddressStatuses|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
