<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[ApplicationsStacks]].
 *
 * @see ApplicationsStacks
 */
class ApplicationsStacksQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ApplicationsStacks[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ApplicationsStacks|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
