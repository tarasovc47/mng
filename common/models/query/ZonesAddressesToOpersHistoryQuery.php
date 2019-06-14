<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\history\ZonesAddressesToOpersHistory]].
 *
 * @see \common\models\history\ZonesAddressesToOpersHistory
 */
class ZonesAddressesToOpersHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\history\ZonesAddressesToOpersHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\history\ZonesAddressesToOpersHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
