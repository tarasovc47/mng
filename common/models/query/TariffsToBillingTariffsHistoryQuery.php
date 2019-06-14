<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\history\TariffsToBillingTariffsHistory]].
 *
 * @see \common\models\history\TariffsToBillingTariffsHistory
 */
class TariffsToBillingTariffsHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\history\TariffsToBillingTariffsHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\history\TariffsToBillingTariffsHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
