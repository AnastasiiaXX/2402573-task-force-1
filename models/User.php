<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

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

 * @property Specialty[] $specialties
 * @property Locations $location
 * @property Responses[] $responses
 * @property Reviews[] $reviews
 * @property Tasks[] $tasks
 * @property Tasks[] $tasks0
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
  public $avatarFile;
  public $category_ids = [];

  public static function findIdentity($id)
  {
    return self::findOne($id);
  }

  public static function findIdentityByAccessToken($token, $type = null)
  {
    throw new \yii\base\NotSupportedException();
  }

  public function getId()
  {
    return $this->getPrimaryKey();
  }

  public function getAuthKey()
  {
    return $this->auth_key;
  }

  public function validateAuthKey($authKey)
  {
    return $this->auth_key === $authKey;
  }

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'users';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['birthday', 'avatar', 'phone_number', 'telegram_name', 'about'], 'default', 'value' => null],
      [['name', 'password', 'email', 'location_id'], 'required'],
      ['email', 'email'],
      [['email'], 'unique'],
      [['created_at'], 'safe'],
      ['birthday', 'compare', 
      'compareValue' => date('Y-m-d', strtotime('-18 years')), 
      'operator' => '<=', 
      'message' => 'Возраст должен быть не менее 18 лет'],
      ['status', 'string', 'max' => 20],
      ['status', 'in', 'range' => ['free', 'busy']],
      [['location_id'], 'integer'],
      [['name'], 'string', 'max' => 100],
      [['password'], 'string', 'max' => 64],
      [['email'], 'string', 'max' => 55],
      [['avatar', 'telegram_name'], 'string', 'max' => 128],
      ['phone_number', 'match', 'pattern' => '/^\d{11}$/'],
      [['about'], 'string', 'max' => 255],
      ['telegram_name', 'string', 'max' => 64],
      [['avatarFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png,jpg,jpeg'],
      [['category_ids'], 'each', 'rule' => ['integer']],
      [['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => Location::class, 'targetAttribute' => ['location_id' => 'id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
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
   * @return \yii\db\ActiveQuery
   */
  public function getLocation()
  {
    return $this->hasOne(Location::class, ['id' => 'location_id']);
  }

  /**
   * Gets query for [[Responses]].
   *
   * @return \yii\db\ActiveQuery
   */
  public function getResponses()
  {
    return $this->hasMany(Response::class, ['worker_id' => 'id']);
  }

  /**
   * Gets query for [[Reviews]].
   *
   * @return \yii\db\ActiveQuery
   */
  public function getReviews()
  {
    return $this->hasMany(Review::class, ['worker_id' => 'id']);
  }

  /**
   * Gets query for [[Tasks]].
   *
   * @return \yii\db\ActiveQuery
   */
  public function getTasks()
  {
    return $this->hasMany(Task::class, ['employer_id' => 'id']);
  }

  /**
   * Gets query for [[Tasks0]].
   *
   * @return \yii\db\ActiveQuery
   */
  public function getTasks0()
  {
    return $this->hasMany(Task::class, ['worker_id' => 'id']);
  }

  public function getCategories()
  {
    return $this->hasMany(Category::class, ['id' => 'category_id'])
      ->viaTable('user_categories', ['user_id' => 'id']);
  }

  public function getCompletedTasksCount(): int
  {
    return $this->getTasks0()
      ->andWhere(['status' => Task::STATUS_COMPLETED])
      ->count();
  }

  public function getFailedTasksCount(): int
  {
    return $this->getTasks0()
      ->andWhere(['status' => Task::STATUS_FAILED])
      ->count();
  }

  /**
   * Сумма оценок всех отзывов
   */
  public function getReviewsScoreSum(): float
  {
    return (float) $this->getReviews()->sum('score');
  }

  /**
   * Количество отзывов
   */
  public function getReviewsCount(): int
  {
    return $this->getReviews()->count();
  }

  /**
   * Средний рейтинг пользователя по формуле:
   * сумма всех оценок / (кол-во отзывов + счетчик проваленных заданий)
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
   * Позиция пользователя в рейтинге исполнителей
   */
  public function getRatingPosition(): int
  {
    $users = self::find()->all();

    usort($users, fn($a, $b) => $b->getAverageRating() <=> $a->getAverageRating());

    foreach ($users as $index => $user) {
      if ($user->id === $this->id) {
        return $index + 1;
      }
    }

    return count($users);
  }

  public function isAvailable(): bool
  {
    return $this->status === 'free';
  }

}
