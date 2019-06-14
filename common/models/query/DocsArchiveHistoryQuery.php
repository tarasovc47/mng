<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[DocsArchiveHistory]].
 *
 * @see DocsArchiveHistory
 */
class DocsArchiveHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return DocsArchiveHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return DocsArchiveHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
