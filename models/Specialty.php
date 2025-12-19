<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "specialties".
 *
 * @property int $id
 * @property string $title
 * @property string $code
 *
 * @property Users[] $users
 */
class Specialty extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'specialties';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'code'], 'required'],
            [['title'], 'string', 'max' => 100],
            [['code'], 'string', 'max' => 50],
            [['title'], 'unique'],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'code' => 'Code',
        ];
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['specialty_id' => 'id']);
    }

}
