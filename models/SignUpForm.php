<?php

namespace app\models;

use app\models\User;
use app\models\Location;
use yii\base\Model;

/**
 * SignUpForm is the model behind the sign up form.
 * @property-read User|null $user
 * @property-read Location|null $location_id
 * @property string $name
 * @property string $email
 * @property int $location_id
 * @property string $password
 * @property string $password_retype
 * @property bool $willRespond
 * 
 */
class SignUpForm extends Model
{
    public $name;
    public $email;
    public $location_id;
    public $password;
    public $password_retype;
    public $willRespond;

    /**
   * @return array the validation rules.
   */
    public function rules(): array
    {
          return [
              [['name'], 'string', 'max' => 85],
              [['name'], 'required'],
              [['email'], 'email'],
              [['email'], 'required'],
              [['email'],
              'unique',
              'targetClass' => User::class,
              'targetAttribute' => 'email',
              'message' => 'Этот адрес электронной почты уже используется'
              ],
              [['location_id'], 'required'],
              [['location_id'], 'integer'],
              [['location_id'],
              'exist',
              'targetClass' => Location::class,
              'targetAttribute' => ['location_id' => 'id']],
              [['password'], 'required'],
              [['password'], 'string', 'min' => 8],
              [['password_retype'], 'required'],
              [['password_retype'],
              'compare',
              'compareAttribute' => 'password',
              'message' => 'Пароли не совпадают'],
              [['willRespond'], 'boolean']
          ];
    }

    /**
     * @return array customized attribute labels (name => label)
     */
    public function attributeLabels(): array
    {
        return [
            'email' => 'Электронная почта',
            'name' => 'Ваше имя',
            'location_id' => 'Город',
            'password' => 'Пароль',
            'password_retype' => 'Повтор пароля',
        ];
    }
}
