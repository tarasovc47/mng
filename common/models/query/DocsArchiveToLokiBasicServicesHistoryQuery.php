<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[DocsArchiveToLokiBasicServicesHistory]].
 *
 * @see DocsArchiveToLokiBasicServicesHistory
 */
class DocsArchiveToLokiBasicServicesHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return DocsArchiveToLokiBasicServicesHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return DocsArchiveToLokiBasicServicesHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
