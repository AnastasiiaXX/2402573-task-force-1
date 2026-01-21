<?php

use yii\db\Migration;

class m260119_033458_fix_categories_pivot extends Migration
{
  /**
   * {@inheritdoc}
   */
    public function safeUp()
    {
        if ($this->db->schema->getTableSchema('users')->getColumn('specialty_id')) {
            $this->dropForeignKey('users_ibfk_1', 'users');
            $this->dropColumn('users', 'specialty_id');
        }

        if ($this->db->schema->getTableSchema('specialties')) {
            $this->dropTable('specialties');
        }
    }

  /**
   * {@inheritdoc}
   */
    public function safeDown()
    {
        echo "m260119_033458_fix_categories_pivot cannot be reverted.\n";

        return false;
    }

  /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260119_033458_fix_categories_pivot cannot be reverted.\n";

        return false;
    }
    */
}
