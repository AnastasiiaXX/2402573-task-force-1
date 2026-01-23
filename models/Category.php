<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "categories".
 *
 * @property int $id
 * @property string $title
 * @property string|null $symbol_code
 *
 * @property Task[] $tasks
 */
class Category extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'categories';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['symbol_code'], 'default', 'value' => null],
            [['title'], 'required'],
            [['title'], 'string', 'max' => 100],
            [['symbol_code'], 'string', 'max' => 50],
            [['title'], 'unique'],
            [['symbol_code'], 'unique'],
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
            'symbol_code' => 'Symbol Code',
        ];
    }

    /**
     * Gets query for tasks in this category.
     *
     * @return ActiveQuery
     */
    public function getTasks(): ActiveQuery
    {
        return $this->hasMany(Task::class, ['category_id' => 'id']);
    }
}
