<?php

use yii\db\Migration;

class m260119_033458_fix_categories_pivot extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey(
            'fk-user_specialties-specialty',
            'user_specialties'
        );

        $this->renameTable(
            'user_specialties',
            'user_categories'
        );

        $this->renameColumn(
            'user_categories',
            'specialty_id',
            'category_id'
        );

        $this->addForeignKey(
            'fk-user_categories-category',
            'user_categories',
            'category_id',
            'categories',
            'id',
            'CASCADE',
            'CASCADE'
        );

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
