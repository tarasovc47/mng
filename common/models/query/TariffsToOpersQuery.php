<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\TariffsToOpers]].
 *
 * @see \common\models\TariffsToOpers
 */
class TariffsToOpersQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\TariffsToOpers[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\TariffsToOpers|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
