<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[Applications]].
 *
 * @see Applications
 */
class ApplicationsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Applications[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Applications|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
