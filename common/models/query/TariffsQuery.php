<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[ZonesTariffs]].
 *
 * @see ZonesTariffs
 */
class TariffsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ZonesTariffs[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ZonesTariffs|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
