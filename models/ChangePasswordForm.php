<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Form model for changing a password
 *
 * @property string $currentPassword
 * @property string $newPassword
 * @property string $newPasswordRepeat
 *
 */
class ChangePasswordForm extends Model
{
    public $currentPassword;
    public $newPassword;
    public $newPasswordRepeat;

    /**
     * @return array validation rules for model attributes
     */
    public function rules(): array
    {
        return [
        [['currentPassword', 'newPassword', 'newPasswordRepeat'], 'required'],
        ['currentPassword', function (string $attribute, ?array $params) {
                /** @var \app\models\User $user */
                $user = Yii::$app->user->identity;
            if (!$user || !Yii::$app->security->validatePassword($this->$attribute, $user->password)) {
                $this->addError($attribute, 'Старый пароль введён неверно');
            }
        }],
        [['newPassword'], 'string', 'min' => 8],
        [['newPasswordRepeat'], 'compare', 'compareAttribute' => 'newPassword',
        'message' => 'Пароли не совпадают'
        ],
        ];
    }

  /**
     * Changes user password
     * 
     * @return bool determines if the password was changed successfully
     */
    public function changePassword(): bool
    {
        if (!$this->validate()) {
            return false;
        }
        /** @var \app\models\User $user */
        $user = Yii::$app->user->identity;

        if (!$user) {
          return false;
        }

        $user->password = Yii::$app->security->generatePasswordHash($this->newPassword);
        return $user->save(false);
    }

    /**
     * @return array customized attribute labels (name => label)
     */
    public function attributeLabels(): array
    {
        return [

        'currentPassword' => 'Текущий пароль',
        'newPassword' => 'Новый пароль',
        'newPasswordRepeat' => 'Введите новый пароль еще раз',
        ];
    }
}
