<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\history\ZonesAddressTypesHistory]].
 *
 * @see \common\models\history\ZonesAddressTypesHistory
 */
class ZonesAddressTypesHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\history\ZonesAddressTypesHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\history\ZonesAddressTypesHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
