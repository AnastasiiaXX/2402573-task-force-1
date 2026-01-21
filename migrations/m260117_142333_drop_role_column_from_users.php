<?php

use yii\db\Migration;

class m260117_142333_drop_role_column_from_users extends Migration
{
  /**
   * {@inheritdoc}
   */
    public function safeUp()
    {
        $this->dropColumn('users', 'role');
    }

  /**
   * {@inheritdoc}
   */
    public function safeDown()
    {
        $this->addColumn('users', 'role', $this->string()->notNull()->defaultValue('customer'));
    }

  /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260117_142333_drop_role_column_from_users cannot be reverted.\n";

        return false;
    }
    */
}
