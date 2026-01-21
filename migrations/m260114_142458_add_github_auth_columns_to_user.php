<?php

use yii\db\Migration;

class m260114_142458_add_github_auth_columns_to_user extends Migration
{
  /**
   * {@inheritdoc}
   */
    public function safeUp()
    {
        $this->addColumn('users', 'auth_key', $this->string(64)->notNull()->defaultValue(''));
        $this->alterColumn('users', 'password', $this->string()->null());
        $this->addColumn('users', 'github_id', $this->bigInteger()->unique()->null());
    }

  /**
   * {@inheritdoc}
   */
    public function safeDown()
    {
        $this->dropColumn('users', 'github_id');
        $this->alterColumn('users', 'password', $this->string()->notNull());
        $this->dropColumn('users', 'auth_key');
    }

  /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260114_142458_add_github_auth_columns_to_user cannot be reverted.\n";

        return false;
    }
    */
}
