<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\history\ZonesAddressesToAgreementsHistory]].
 *
 * @see \common\models\history\ZonesAddressesToAgreementsHistory
 */
class ZonesAddressesToAgreementsHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\history\ZonesAddressesToAgreementsHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\history\ZonesAddressesToAgreementsHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
