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
 * @property string $role
 * @property string|null $birthday
 * @property string|null $avatar
 * @property string|null $phone_number
 * @property string|null $telegram_name
 * @property string|null $about
 * @property int $location_id
 * @property int|null $specialty_id
 * @property int $failed_tasks

 *
 * @property Locations $location
 * @property Responses[] $responses
 * @property Reviews[] $reviews
 * @property Specialties $specialty
 * @property Tasks[] $tasks
 * @property Tasks[] $tasks0
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
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
            [['birthday', 'avatar', 'phone_number', 'telegram_name', 'about', 'specialty_id'], 'default', 'value' => null],
            [['name', 'password', 'email', 'role', 'location_id'], 'required'],
            [['birthday'], 'safe'],
            [['location_id', 'specialty_id'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['password'], 'string', 'max' => 64],
            [['email', 'role'], 'string', 'max' => 55],
            [['avatar', 'telegram_name'], 'string', 'max' => 128],
            [['phone_number'], 'string', 'max' => 11],
            [['about'], 'string', 'max' => 255],
            [['email'], 'unique'],
            [['specialty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Specialty::class, 'targetAttribute' => ['specialty_id' => 'id']],
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
            'name' => 'Name',
            'password' => 'Password',
            'email' => 'Email',
            'role' => 'Role',
            'birthday' => 'Birthday',
            'avatar' => 'Avatar',
            'phone_number' => 'Phone Number',
            'telegram_name' => 'Telegram Name',
            'about' => 'About',
            'location_id' => 'Location ID',
            'specialty_id' => 'Specialty ID',
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
     * Gets query for [[Specialty]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSpecialty()
    {
        return $this->hasOne(Specialty::class, ['id' => 'specialty_id']);
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

}
