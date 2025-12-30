<?php

namespace app\models;

use yii\base\Model;
use app\models\Category;

class AddTaskForm extends Model
{
  public $title = '';
  public $category_id;
  public $description = '';
  public $files;
  public $location = '';
  public $cost = '';
  public $date_end = '';

  public function rules()
  {
    return [
      [['title', 'description', 'category_id'], 'required'],
      ['title', 'validateMinNonWhitespace', 'min' => 10],
      ['description', 'validateMinNonWhitespace', 'min' => 30],
      [
        ['category_id'],
        'exist',
        'targetClass' => Category::class,
        'targetAttribute' => ['category_id' => 'id']
      ],
      ['cost', 'integer', 'min' => 1],
      [['date_end'], 'date', 'format' => 'php:Y-m-d'],
      [['date_end'], 'validateDeadline'],
      [
        ['files'],
        'file',
        'skipOnEmpty' => true,
        'maxFiles' => 5,
      ]
    ];
  }

  public function validateDeadline($attribute)
  {
    if ($this->$attribute && strtotime($this->$attribute) < strtotime(date('Y-m-d'))) {
      $this->addError($attribute, 'Дата не может быть раньше текущего дня');
    }
  }

  public function validateMinNonWhitespace($attribute, $params)
  {
    $min = $params['min'] ?? 1;
    $text = preg_replace('/\s+/u', '', (string)$this->$attribute);

    if (mb_strlen($text) < $min) {
      $this->addError($attribute, "Минимум {$min} непробельных символов.");
    }
  }

  public function attributeLabels()
  {
    return [
      'title' => 'Суть работы',
      'description' => 'Подробности задания',
      'category_id' => 'Категория',
      'location_id' => 'Локация',
      'cost' => 'Бюджет',
      'date_end' => 'Срок исполнения',
    ];
  }
}
