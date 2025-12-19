<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "responses".
 *
 * @property int $id
 * @property string|null $date_add
 * @property int|null $cost
 * @property string|null $comment
 * @property int|null $worker_id
 * @property int|null $task_id
 *
 * @property Tasks $task
 * @property Users $worker
 */
class Response extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'responses';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cost', 'comment', 'worker_id', 'task_id'], 'default', 'value' => null],
            [['date_add'], 'safe'],
            [['cost', 'worker_id', 'task_id'], 'integer'],
            [['comment'], 'string'],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::class, 'targetAttribute' => ['task_id' => 'id']],
            [['worker_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['worker_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date_add' => 'Date Add',
            'cost' => 'Cost',
            'comment' => 'Comment',
            'worker_id' => 'Worker ID',
            'task_id' => 'Task ID',
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

}
