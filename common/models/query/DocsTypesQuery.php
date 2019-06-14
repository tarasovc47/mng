<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[DocsTypes]].
 *
 * @see DocsTypes
 */
class DocsTypesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return DocsTypes[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return DocsTypes|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
