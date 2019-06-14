<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\history\ZonesAddressesHistory]].
 *
 * @see \common\models\history\ZonesAddressesHistory
 */
class ZonesAddressesHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\history\ZonesAddressesHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\history\ZonesAddressesHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
