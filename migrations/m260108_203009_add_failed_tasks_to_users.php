<?php

use yii\db\Migration;

class m260108_203009_add_failed_tasks_to_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->addColumn(
            'users',
            'failed_tasks',
            $this->integer()->notNull()->defaultValue(0)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
         $this->dropColumn('users', 'failed_tasks');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260108_203009_add_failed_tasks_to_users cannot be reverted.\n";

        return false;
    }
    */
}
