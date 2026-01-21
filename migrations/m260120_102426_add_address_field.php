<?php

use yii\db\Migration;

class m260120_102426_add_address_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            'tasks',
            'address',
            $this->string()->null()->after('location_id')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('tasks', 'address');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260120_102426_add_address_field cannot be reverted.\n";

        return false;
    }
    */
}
