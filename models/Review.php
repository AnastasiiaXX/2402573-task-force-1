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
            [['text'], 'string', 'max' => 128],
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
            'text' => 'Text',
            'score' => 'Score',
            'employer_id' => 'Employer ID',
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
