<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[ContactFaces]].
 *
 * @see ContactFaces
 */
class ContactFacesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ContactFaces[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ContactFaces|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
