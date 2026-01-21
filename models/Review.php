<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reviews".
 *
 * @property int $id
 * @property string|null $date_add
 * @property string $text
 * @property int $score
 * @property int|null $employer_id
 * @property int|null $worker_id
 * @property int|null $task_id
 *
 * @property Tasks $task
 * @property Users $worker
 */
class Review extends \yii\db\ActiveRecord
{
  /**
   * {@inheritdoc}
   */
    public static function tableName()
    {
        return 'reviews';
    }

  /**
   * {@inheritdoc}
   */
    public function rules()
    {
        return [
        [['employer_id', 'worker_id', 'task_id'], 'default', 'value' => null],
        [['date_add'], 'safe'],
        [['text', 'score'], 'required'],
        [['score', 'employer_id', 'worker_id', 'task_id'], 'integer'],
        ['score', 'integer', 'min' => 1, 'max' => 5],
        [['text'], 'string', 'max' => 128],
        [['task_id'], 'exist',
        'skipOnError' => true,
        'targetClass' => Task::class,
        'targetAttribute' => ['task_id' => 'id']],
        [['worker_id'], 'exist',
        'skipOnError' => true,
        'targetClass' => User::class,
        'targetAttribute' => ['worker_id' => 'id']],
        ];
    }

  /**
   * {@inheritdoc}
   */
    public function attributeLabels()
    {
        return [
        'text' => 'Комментарий',
        'score' => 'Оценка',
        ];
    }

  /**
   * Gets query for [[Task]].
   *
   * @return \yii\db\ActiveQuery
   */
    public function getTask()
    {
        return $this->hasOne(Task::class, ['id' => 'task_id']);
    }

  /**
   * Gets query for [[Worker]].
   *
   * @return \yii\db\ActiveQuery
   */
    public function getWorker()
    {
        return $this->hasOne(User::class, ['id' => 'worker_id']);
    }

  /**
 * Получить заказчика (автора отзыва)
 *
 * @return \yii\db\ActiveQuery
 */
    public function getEmployer()
    {
        return $this->hasOne(User::class, ['id' => 'employer_id']);
    }
}
