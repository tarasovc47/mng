<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\history\ZonesAddressesToTariffsHistory]].
 *
 * @see \common\models\history\ZonesAddressesToTariffsHistory
 */
class ZonesAddressesToTariffsHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\history\ZonesAddressesToTariffsHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\history\ZonesAddressesToTariffsHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
