<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[ZonesFloorsToApartments]].
 *
 * @see ZonesFloorsToApartments
 */
class ZonesFlatsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ZonesFloorsToApartments[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ZonesFloorsToApartments|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
