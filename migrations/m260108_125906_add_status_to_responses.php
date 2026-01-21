<?php

use yii\db\Migration;

class m260108_125906_add_status_to_responses extends Migration
{
  /**
   * {@inheritdoc}
   */
    public function safeUp()
    {
        $this->addColumn('responses', 'status', $this->string(16)->notNull()->defaultValue('new'));
    }

  /**
   * {@inheritdoc}
   */
    public function safeDown()
    {
        $this->dropColumn('responses', 'status');
    }

  /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260108_125906_add_status_to_responses cannot be reverted.\n";

        return false;
    }
    */
}
