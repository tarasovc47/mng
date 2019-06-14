<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[DocsArchive]].
 *
 * @see DocsArchive
 */
class DocsArchiveQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return DocsArchive[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return DocsArchive|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
