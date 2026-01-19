<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_specialties}}`.
 */
class m260117_232005_create_user_specialties_table extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->createTable('user_specialties', [
      'user_id' => $this->integer()->notNull(),
      'specialty_id' => $this->integer()->notNull(),
      'PRIMARY KEY(user_id, specialty_id)',
    ]);

    $this->addForeignKey('fk-user_specialties-user', 'user_specialties', 'user_id', 'users', 'id', 'CASCADE', 'CASCADE');
    $this->addForeignKey('fk-user_specialties-specialty', 'user_specialties', 'specialty_id', 'specialties', 'id', 'CASCADE', 'CASCADE');
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->dropForeignKey('fk-user_specialties-user', 'user_specialties');
    $this->dropForeignKey('fk-user_specialties-specialty', 'user_specialties');
    $this->dropTable('user_specialties');
  }
}
