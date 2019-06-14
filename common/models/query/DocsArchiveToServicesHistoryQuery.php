<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\history\DocsArchiveToServicesHistory]].
 *
 * @see \common\models\history\DocsArchiveToServicesHistory
 */
class DocsArchiveToServicesHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\history\DocsArchiveToServicesHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\history\DocsArchiveToServicesHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
