<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $name
 * @property string $password
 * @property string $email
 * @property string|null $birthday
 * @property string|null $avatar
 * @property string|null $created_at
 * @property string|null $status
 * @property string|null $phone_number
 * @property string|null $telegram_name
 * @property string|null $about
 * @property int $location_id
 * @property int $failed_tasks
 * @property string|null $auth_key
 *
 * @property Category[] $categories
 * @property Location $location
 * @property Response[] $responses
 * @property Review[] $reviews
 * @property Task[] $tasks
 * @property Task[] $tasks0
 */
class User extends ActiveRecord implements IdentityInterface
{
  /** @var mixed Avatar file for uploading */
    public $avatarFile;

  /** @var array List of chosen categories */
    public $category_ids = [];

  /**
   * {@inheritdoc}
   */
    public static function findIdentity($id): ?IdentityInterface
    {
        return self::findOne($id);
    }

  /**
   * {@inheritdoc}
   * @throws NotSupportedException
   */
    public static function findIdentityByAccessToken($token, $type = null): void
    {
        throw new NotSupportedException();
    }

  /**
   * {@inheritdoc}
   */
    public function getId(): int
    {
        return $this->getPrimaryKey();
    }

  /**
   * {@inheritdoc}
   */
    public function getAuthKey(): ?string
    {
        return $this->auth_key;
    }

  /**
   * {@inheritdoc}
   */
    public function validateAuthKey($authKey): bool
    {
        return $this->auth_key === $authKey;
    }

  /**
   * {@inheritdoc}
   */
    public static function tableName(): string
    {
        return 'users';
    }

  /**
   * {@inheritdoc}
   */
    public function rules(): array
    {
        return [
        [['birthday', 'avatar', 'phone_number', 'telegram_name', 'about'], 'default', 'value' => null],
        [['name', 'password', 'email', 'location_id'], 'required'],
        ['email', 'email'],
        [['email'], 'unique'],
        [['created_at'], 'safe'],
        [
        'birthday',
        'compare',
        'compareValue' => date('Y-m-d', strtotime('-18 years')),
        'operator' => '<=',
        'message' => 'Возраст должен быть не менее 18 лет'
        ],
        ['status', 'string', 'max' => 20],
        ['status', 'in', 'range' => ['free', 'busy']],
        [['location_id'], 'integer'],
        [['name'], 'string', 'max' => 100],
        [['password'], 'string', 'max' => 255],
        [['email'], 'string', 'max' => 55],
        [['avatar', 'telegram_name'], 'string', 'max' => 128],
        ['phone_number', 'match', 'pattern' => '/^\d{11}$/'],
        [['about'], 'string', 'max' => 255],
        ['telegram_name', 'string', 'max' => 64],
        [['avatarFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png,jpg,jpeg'],
        [['category_ids'], 'each', 'rule' => ['integer']],
        [['location_id'], 'exist',
        'skipOnError' => true,
        'targetClass' => Location::class,
        'targetAttribute' => ['location_id' => 'id']],
        ];
    }

  /**
   * {@inheritdoc}
   */
    public function attributeLabels(): array
    {
        return [
        'id' => 'ID',
        'name' => 'Имя',
        'password' => 'Пароль',
        'email' => 'Email',
        'birthday' => 'Дата рождения',
        'avatar' => 'Аватар',
        'phone_number' => 'Номер телефона',
        'telegram_name' => 'Имя в Telegram',
        'about' => 'О себе',
        'location_id' => 'Location ID',
        'specialty_ids' => 'Специализации',
        ];
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
        return $this->hasMany(Response::class, ['worker_id' => 'id']);
    }

  /**
   * Gets query for [[Reviews]].
   *
   * @return ActiveQuery
   */
    public function getReviews(): ActiveQuery
    {
        return $this->hasMany(Review::class, ['worker_id' => 'id']);
    }

  /**
   * Gets query for [[Tasks]].
   *
   * @return ActiveQuery
   */
    public function getTasks(): ActiveQuery
    {
        return $this->hasMany(Task::class, ['employer_id' => 'id']);
    }

  /**
   * Gets query for [[Tasks0]].
   *
   * @return ActiveQuery
   */
    public function getTasks0(): ActiveQuery
    {
        return $this->hasMany(Task::class, ['worker_id' => 'id']);
    }

  /**
   * Gets query for Categories.
   *
   * @return ActiveQuery
   */
    public function getCategories(): ActiveQuery
    {
        return $this->hasMany(Category::class, ['id' => 'category_id'])
        ->viaTable('user_categories', ['user_id' => 'id']);
    }

  /**
   * 
   * @return int the sum of tasks completed
   */
    public function getCompletedTasksCount(): int
    {
        return $this->getTasks0()
        ->andWhere(['status' => Task::STATUS_COMPLETED])
        ->count();
    }

    /**
   * 
   * @return int the sum of tasks failed
   */
    public function getFailedTasksCount(): int
    {
        return $this->getTasks0()
        ->andWhere(['status' => Task::STATUS_FAILED])
        ->count();
    }

  /**
   * @return float the sum of all reviews
   */
    public function getReviewsScoreSum(): float
    {
        return (float) $this->getReviews()->sum('score');
    }

  /**
   * The count of reviews
   * @return int
   */
    public function getReviewsCount(): int
    {
        return $this->getReviews()->count();
    }

  /**
   * 
   * The average rating of the user calculated with the formula below:
   * the sum of all scores / (the number of all reviews + count of failed tasks)
   * @return float
   */
    public function getAverageRating(): float
    {
        $reviewsCount = $this->getReviewsCount();
        $failedTasks = $this->getFailedTasksCount();

        $denominator = $reviewsCount + $failedTasks;
        if ($denominator === 0) {
            return 0;
        }

        return $this->getReviewsScoreSum() / $denominator;
    }

  /**
   * @return int the user position in the rating
   */
    public function getRatingPosition(): int
    {   
      /** @var User[] $users */
        $users = self::find()->all();

        usort($users, fn($a, $b) => $b->getAverageRating() <=> $a->getAverageRating());

        foreach ($users as $index => $user) {
            if ($user->id === $this->id) {
                return $index + 1;
            }
        }

        return count($users);
    }

    /**
     * Checks if the user is available for taking tasks.
     * @return bool
     */
    public function isAvailable(): bool
    {
        return !$this->getTasks0()
        ->andWhere(['status' => Task::STATUS_IN_PROGRESS])
        ->exists();
    }

    /**
     * @return int|null the age of the user and null if the birthday is not put
     */
    public function getAge(): ?int
    {
        if (!$this->birthday) {
            return null;
        }
        $birthday = new \DateTime($this->birthday);
        $now = new \DateTime();
        return $now->diff($birthday)->y;
    }
}
