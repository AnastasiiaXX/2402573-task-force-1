<?php

namespace app\models;

use Yii;
use yii\base\Model;

class ChangePasswordForm extends Model
{
    public $currentPassword;
    public $newPassword;
    public $newPasswordRepeat;

    public function rules()
    {
        return [
        [['currentPassword', 'newPassword', 'newPasswordRepeat'], 'required'],
        ['currentPassword', function ($attribute, $params) {
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
     * change user password
     * @return bool
     */
    public function changePassword()
    {
        if (!$this->validate()) {
            return false;
        }
        /** @var \app\models\User $user */
        $user = Yii::$app->user->identity;
        $user->password = Yii::$app->security->generatePasswordHash($this->newPassword);
        return $user->save(false);
    }

    public function attributeLabels()
    {
        return [

        'currentPassword' => 'Текущий пароль',
        'newPassword' => 'Новый пароль',
        'newPasswordRepeat' => 'Введите новый пароль еще раз',
        ];
    }
}
