<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\history\TariffsHistory]].
 *
 * @see \common\models\history\TariffsHistory
 */
class TariffsHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\history\TariffsHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\history\TariffsHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
