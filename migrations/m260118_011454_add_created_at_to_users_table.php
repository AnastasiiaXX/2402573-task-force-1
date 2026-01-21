<?php

use yii\db\Migration;

class m260118_011454_add_created_at_to_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('users', 'created_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP')->after('avatar'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('users', 'created_at');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260118_011454_add_created_at_to_users_table cannot be reverted.\n";

        return false;
    }
    */
}
