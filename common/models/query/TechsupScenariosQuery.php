<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[TechsupScenarios]].
 *
 * @see TechsupScenarios
 */
class TechsupScenariosQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return TechsupScenarios[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return TechsupScenarios|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
