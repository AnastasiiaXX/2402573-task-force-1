<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tasks".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int|null $cost
 * @property string|null $date_add
 * @property string|null $date_end
 * @property string|null $status
 * @property int $employer_id
 * @property int|null $worker_id
 * @property int|null $location_id
 * @property int|null $category_id
 *
 * @property Category $category
 * @property User $employer
 * @property File[] $files
 * @property Location $location
 * @property Response[] $responses
 * @property Review[] $reviews
 * @property User $worker
 */
class Task extends ActiveRecord
{
    /**
     * ENUM field values
     */
    const STATUS_NEW = 'new';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELED = 'canceled';
    const STATUS_FAILED = 'failed';

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'tasks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['cost', 'date_end', 'worker_id', 'location_id', 'category_id'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'new'],
            [['title', 'description', 'employer_id'], 'required'],
            [['description', 'status'], 'string'],
            [['cost', 'employer_id', 'worker_id', 'location_id', 'category_id'], 'integer'],
            [['date_add', 'date_end'], 'safe'],
            [['title'], 'string', 'max' => 200],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
            [['employer_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['employer_id' => 'id']],
            [['worker_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['worker_id' => 'id']],
            [['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => Location::class, 'targetAttribute' => ['location_id' => 'id']],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
            'cost' => 'Cost',
            'date_add' => 'Date Add',
            'date_end' => 'Date End',
            'status' => 'Status',
            'employer_id' => 'Employer ID',
            'worker_id' => 'Worker ID',
            'location_id' => 'Location ID',
            'category_id' => 'Category ID',
        ];
    }

    /**
     * Gets query for [[Category]].
     *
     * @return ActiveQuery
     */
    public function getCategory(): ActiveQuery
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * Gets query for [[Employer]].
     *
     * @return ActiveQuery
     */
    public function getEmployer(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'employer_id']);
    }

    /**
     * Gets query for [[Files]].
     *
     * @return ActiveQuery
     */
    public function getFiles(): ActiveQuery
    {
        return $this->hasMany(File::class, ['task_id' => 'id']);
    }

    /**
     * Gets query for [[Location]].
     *
     * @return ActiveQuery
     */
    public function getLocation(): ActiveQuery
    {
        return $this->hasOne(Location::class, ['id' => 'location_id']);
    }

    /**
     * Gets query for [[Responses]].
     *
     * @return ActiveQuery
     */
    public function getResponses(): ActiveQuery
    {
        return $this->hasMany(Response::class, ['task_id' => 'id']);
    }

    /**
     * Gets query for [[Reviews]].
     *
     * @return ActiveQuery
     */
    public function getReviews(): ActiveQuery
    {
        return $this->hasMany(Review::class, ['task_id' => 'id']);
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


    /**
     * Returns the list of all available task statuses and their labels
     * @return array<string, string> status values and their human-readable labels
     */
    public static function optsStatus(): array
    {
        return [
            self::STATUS_NEW => 'Новое',
            self::STATUS_IN_PROGRESS => 'В работе',
            self::STATUS_COMPLETED => 'Завершено',
            self::STATUS_CANCELED => 'Отменено',
            self::STATUS_FAILED => 'Провалено',
        ];
    }

    /**
     * Returns the label of the current task status
     * 
     * @return string
     */
    public function displayStatus(): string
    {
        $statuses = self::optsStatus();
        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * @return bool
     */
    public function isStatusNew(): bool
    {
        return $this->status === self::STATUS_NEW;
    }

    public function setStatusToNew(): void
    {
        $this->status = self::STATUS_NEW;
    }

    /**
     * @return bool
     */
    public function isStatusInprogress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    public function setStatusToInprogress(): void
    {
        $this->status = self::STATUS_IN_PROGRESS;
    }

    /**
     * @return bool
     */
    public function isStatusCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function setStatusToCompleted(): void
    {
        $this->status = self::STATUS_COMPLETED;
    }

    /**
     * @return bool
     */
    public function isStatusCanceled(): bool
    {
        return $this->status === self::STATUS_CANCELED;
    }

    public function setStatusToCanceled(): void
    {
        $this->status = self::STATUS_CANCELED;
    }

    /**
     * @return bool
     */
    public function isStatusFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function setStatusToFailed(): void
    {
        $this->status = self::STATUS_FAILED;
    }
}
