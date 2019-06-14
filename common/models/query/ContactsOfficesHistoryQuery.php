<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[ContactsOfficesHistory]].
 *
 * @see ContactsOfficesHistory
 */
class ContactsOfficesHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ContactsOfficesHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ContactsOfficesHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
