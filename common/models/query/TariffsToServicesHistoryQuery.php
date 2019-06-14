<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\history\TariffsToServicesHistory]].
 *
 * @see \common\models\history\TariffsToServicesHistory
 */
class TariffsToServicesHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\history\TariffsToServicesHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\history\TariffsToServicesHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
