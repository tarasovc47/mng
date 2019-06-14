<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\history\ZonesPorchesHistory]].
 *
 * @see \common\models\history\ZonesPorchesHistory
 */
class ZonesPorchesHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\history\ZonesPorchesHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\history\ZonesPorchesHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
