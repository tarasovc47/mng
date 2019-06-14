<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\history\DocsArchiveToConnectionTechnologiesHistory]].
 *
 * @see \common\models\history\DocsArchiveToConnectionTechnologiesHistory
 */
class DocsArchiveToConnectionTechnologiesHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\history\DocsArchiveToConnectionTechnologiesHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\history\DocsArchiveToConnectionTechnologiesHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
