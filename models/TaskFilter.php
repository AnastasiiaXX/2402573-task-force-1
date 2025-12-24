<?php

namespace app\models;

use yii\base\Model;

class TaskFilter extends Model
{

  public $categories = [];
  public $notTaken = false;
  public $timePeriod = '';

  public function rules() 
  {
    return [
            ['categories', 'each', 'rule' => ['integer']],
            ['notTaken', 'boolean'],
            ['timePeriod', 'string'],
        ];
  }

  public function attributeLabels()
  {
    return [
      'categories' => 'Категории',
      'notTaken' => 'Без исполнителя',
      'timePeriod' => 'Период'
    ];
  }
}
