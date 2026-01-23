<?php

namespace app\models;

use yii\base\Model;

/**
 * TaskFilter is the model for filter form.

 * @property array $categories
 * @property string $timePeriod
 * @property bool $notTaken
 * 
 */
class TaskFilter extends Model
{
    public $categories = [];
    public $notTaken = false;
    public $timePeriod = '';

    /**
   * @return array the validation rules.
   */
    public function rules(): array
    {
        return [
            ['categories', 'each', 'rule' => ['integer']],
            ['notTaken', 'boolean'],
            ['timePeriod', 'string'],
        ];
    }

    /**
     * @return array customized attribute labels (name => label)
     */
    public function attributeLabels(): array
    {
        return [
        'categories' => 'Категории',
        'notTaken' => 'Без исполнителя',
        'timePeriod' => 'Период'
        ];
    }
}
