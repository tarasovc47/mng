<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[ApplicationsAttributes]].
 *
 * @see ApplicationsAttributes
 */
class ApplicationsAttributesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ApplicationsAttributes[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ApplicationsAttributes|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
