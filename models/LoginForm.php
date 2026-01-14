<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read User|null $user
 *
 */
class LoginForm extends Model
{
  public $email;
  public $password;

  private $_user = null;


  /**
   * @return array the validation rules.
   */
  public function rules()
  {
    return [
      [['email', 'password'], 'required'],
      ['email', 'email', 'message' => 'Некорректный адрес электронной почты'],
      ['password', 'validatePassword'],
    ];
  }


  /**
   * Finds user by [[email]]
   *
   * @return User|null
   */
  public function getUser()
  {
    if ($this->_user === null) {
      $this->_user = User::findOne(['email' => $this->email]);
    }

    return $this->_user;
  }

  /**
   * Validates the password.
   * This method serves as the inline validation for password.
   *
   * @param string $attribute the attribute currently being validated
   * @param array $params the additional name-value pairs given in the rule
   */
  public function validatePassword($attribute, $params)
  {
    if (!$this->hasErrors()) {
      $user = $this->getUser();

      if (!$user || !\Yii::$app->security->validatePassword($this->password, $user->password)) {
        $this->addError($attribute, 'Неправильный email или пароль');
      }
    }
  }

  public function attributeLabels()
  {
    return [
      'email' => 'Email',
      'password' => 'Пароль',
    ];
  }
}
