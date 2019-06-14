<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[Zones]].
 *
 * @see Zones
 */
class ZonesAddressesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Zones[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Zones|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
