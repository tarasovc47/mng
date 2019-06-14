<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\history\TariffsToGroupsHistory]].
 *
 * @see \common\models\history\TariffsToGroupsHistory
 */
class TariffsToGroupsHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\history\TariffsToGroupsHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\history\TariffsToGroupsHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
