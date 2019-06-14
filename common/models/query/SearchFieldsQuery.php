<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[SearchFields]].
 *
 * @see SearchFields
 */
class SearchFieldsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return SearchFields[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return SearchFields|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
