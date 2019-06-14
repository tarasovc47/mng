<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[ContactsOffices]].
 *
 * @see ContactsOffices
 */
class ContactsOfficesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ContactsOffices[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ContactsOffices|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
