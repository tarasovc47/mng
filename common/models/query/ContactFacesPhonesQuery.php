<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[ContactFacesPhones]].
 *
 * @see ContactFacesPhones
 */
class ContactFacesPhonesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ContactFacesPhones[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ContactFacesPhones|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
