<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[DistrictsAndAreas]].
 *
 * @see DistrictsAndAreas
 */
class ZonesDistrictsAndAreasQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return DistrictsAndAreas[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return DistrictsAndAreas|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
