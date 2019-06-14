<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\ZonesAddressesToOpers]].
 *
 * @see \common\models\ZonesAddressesToOpers
 */
class ZonesAddressesToOpersQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\ZonesAddressesToOpers[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\ZonesAddressesToOpers|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
