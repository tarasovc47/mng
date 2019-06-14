<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[CasUser]].
 *
 * @see CasUser
 */
class CasUserQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return CasUser[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return CasUser|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
