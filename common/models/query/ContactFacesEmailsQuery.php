<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[ContactFacesEmails]].
 *
 * @see ContactFacesEmails
 */
class ContactFacesEmailsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ContactFacesEmails[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ContactFacesEmails|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
