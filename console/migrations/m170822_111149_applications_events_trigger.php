<?php

use yii\db\Migration;

class m170822_111149_applications_events_trigger extends Migration
{
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE FUNCTION notify_application_event() RETURNS trigger AS
            $BODY$
            BEGIN
              PERFORM pg_notify(\'applications_events_insert\', row_to_json(NEW)::text);
              RETURN new;
            END;
            $BODY$
            LANGUAGE \'plpgsql\' VOLATILE COST 100;
        ');

        $this->execute('
            CREATE TRIGGER applications_events_after
            AFTER INSERT
            ON applications_events
            FOR EACH ROW
            EXECUTE PROCEDURE notify_application_event();
        ');
    }

    public function safeDown()
    {
        echo "m170822_111149_applications_events_trigger cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170822_111149_applications_events_trigger cannot be reverted.\n";

        return false;
    }
    */
}
