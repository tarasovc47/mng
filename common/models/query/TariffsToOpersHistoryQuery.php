<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\history\TariffsToOpersHistory]].
 *
 * @see \common\models\history\TariffsToOpersHistory
 */
class TariffsToOpersHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\history\TariffsToOpersHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\history\TariffsToOpersHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
