<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[DocsArchiveToLokiBasicServices]].
 *
 * @see DocsArchiveToLokiBasicServices
 */
class DocsArchiveToLokiBasicServicesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return DocsArchiveToLokiBasicServices[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return DocsArchiveToLokiBasicServices|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
