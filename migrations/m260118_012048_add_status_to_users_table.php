<?php

use yii\db\Migration;

class m260118_012048_add_status_to_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->addColumn('users', 'status', $this->string(20)->notNull()->defaultValue('free')->after('created_at'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('users', 'status');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260118_012048_add_status_to_users_table cannot be reverted.\n";

        return false;
    }
    */
}
