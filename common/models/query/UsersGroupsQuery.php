<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\UsersGroups]].
 *
 * @see \common\models\UsersGroups
 */
class UsersGroupsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\UsersGroups[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\UsersGroups|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
