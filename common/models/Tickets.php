<?php

namespace common\models;

use Yii;

class Tickets extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'tickets';
    }

    public function rules()
    {
        return [
            [['name', 'planned_start_on', 'start_on', 'user_id'], 'required'],
            [['planned_start_on', 'start_on', 'planned_end_on', 'end_on', 'stalled_ticket_id', 'parent_ticket_id', 'user_id', 'author_id', 'reviewer_id', 'ticketable_id'], 'integer'],
            [['description', 'on_complete'], 'string'],
            [['name', 'ticketable_type'], 'string', 'max' => 255],
            ['planned_end_on', 'compare', 'compareAttribute' => 'planned_start_on', 'operator' => '>'],
            ['end_on', 'compare', 'compareAttribute' => 'start_on', 'operator' => '>'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Name',
            'planned_start_on' => 'Planned Start On',
            'start_on' => 'Start On',
            'planned_end_on' => 'Planned End On',
            'end_on' => 'End On',
            'description' => 'Description',
            'on_complete' => 'On Complete',
            'stalled_ticket_id' => 'Stalled Ticket ID',
            'parent_ticket_id' => 'Parent Ticket ID',
            'user_id' => 'User ID',
            'author_id' => 'Author ID',
            'reviewer_id' => 'Reviewer ID',
            'ticketable_id' => 'Ticketable ID',
            'ticketable_type' => 'Ticketable Type',
        ];
    }

    public static function find()
    {
        return new \common\models\query\TicketsQuery(get_called_class());
    }
}
