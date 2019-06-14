<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[ZonesAddressesToPorches]].
 *
 * @see ZonesAddressesToPorches
 */
class ZonesPorchesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ZonesAddressesToPorches[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ZonesAddressesToPorches|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
