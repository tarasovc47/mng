<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[AddressesRecicle]].
 *
 * @see AddressesRecicle
 */
class AddressesRecycleQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return AddressesRecicle[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return AddressesRecicle|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
