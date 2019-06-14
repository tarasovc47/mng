<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[DocsTypesHistory]].
 *
 * @see DocsTypesHistory
 */
class DocsTypesHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return DocsTypesHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return DocsTypesHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
