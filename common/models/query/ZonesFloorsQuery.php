<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[ZonesPorchesToFloors]].
 *
 * @see ZonesPorchesToFloors
 */
class ZonesFloorsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ZonesPorchesToFloors[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ZonesPorchesToFloors|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
