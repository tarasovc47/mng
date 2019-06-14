<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\history\TariffsGroupsHistory]].
 *
 * @see \common\models\history\TariffsGroupsHistory
 */
class TariffsGroupsHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\history\TariffsGroupsHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\history\TariffsGroupsHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
