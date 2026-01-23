<?php

namespace app\models;

use app\models\User;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "responses".
 *
 * @property int $id
 * @property string|null $date_add
 * @property int|null $cost
 * @property string|null $comment
 * @property int|null $worker_id
 * @property int|null $task_id
 * @property string $status
 *
 * @property Task $task
 * @property Users $worker
 */
class Response extends ActiveRecord
{
    public const STATUS_NEW = 'new';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';


  /**
   * {@inheritdoc}
   */
    public static function tableName(): string
    {
        return 'responses';
    }

  /**
   * {@inheritdoc}
   */
    public function rules(): array
    {
        return [
        ['status', 'default', 'value' => self::STATUS_NEW],
        ['status', 'in', 'range' => [self::STATUS_NEW, self::STATUS_ACCEPTED, self::STATUS_REJECTED]],
        [['cost', 'comment', 'worker_id', 'task_id'], 'default', 'value' => null],
        [['date_add'], 'safe'],
        [['worker_id', 'task_id'], 'integer'],
        [['cost'], 'integer', 'min' => 1, 'message' => 'Цена должна быть целым числом больше нуля'],
        [['comment'], 'string'],
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
    public function attributeLabels(): array
    {
        return [
        'cost' => 'Ваша цена',
        'comment' => 'Комментарий',
        ];
    }

  /**
   * Gets query for [[Task]].
   *
   * @return ActiveQuery
   */
    public function getTask(): ActiveQuery
    {
        return $this->hasOne(Task::class, ['id' => 'task_id']);
    }

  /**
   * Gets query for [[Worker]].
   *
   * @return ActiveQuery
   */
    public function getWorker(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'worker_id']);
    }
}
