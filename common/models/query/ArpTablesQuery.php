<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\ArpTables]].
 *
 * @see \common\models\BackboneNodes
 */
class ArpTablesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\ArpTables[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\ArpTables|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
