<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[SearchFieldsSettings]].
 *
 * @see SearchFieldsSettings
 */
class SearchFieldsSettingsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return SearchFieldsSettings[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return SearchFieldsSettings|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
