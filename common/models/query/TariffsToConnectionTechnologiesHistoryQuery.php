<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\history\TariffsToConnectionTechnologiesHistory]].
 *
 * @see \common\models\history\TariffsToConnectionTechnologiesHistory
 */
class TariffsToConnectionTechnologiesHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\history\TariffsToConnectionTechnologiesHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\history\TariffsToConnectionTechnologiesHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
