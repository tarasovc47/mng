<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\history\ContactFacesHistory]].
 *
 * @see \common\models\history\ContactFacesHistory
 */
class ContactFacesHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\history\ContactFacesHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\history\ContactFacesHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
