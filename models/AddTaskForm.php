<?php

namespace app\models;

use yii\base\Model;
use app\models\Category;

/**
 * Form model for adding a new task.
 * * @property string $title
 * @property int $category_id
 * @property string $description
 * @property UploadedFile[] $files
 * @property int|string $cost
 * @property string $date_end
 * @property string $location
 * @property string $address
 * @property string $city
 * @property float $latitude
 * @property float $longitude
 */

class AddTaskForm extends Model
{
    public $title = '';
    public $category_id;
    public $description = '';
  /** @var UploadedFile[] */
    public $files = [];
    public $cost = '';
    public $date_end = '';
    public $location;
    public $address;
    public $city;
    public $latitude;
    public $longitude;


    /**
     * @return array validation rules for model attributes
     */
    public function rules(): array
    {
        return [
        [['title', 'description', 'category_id'], 'required'],
        ['title', 'validateMinNonWhitespace', 'params' => ['min' => 10]],
        ['description', 'validateMinNonWhitespace', 'params' => ['min' => 30]],
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
        ],
        [['location', 'latitude', 'longitude', 'city', 'address'], 'safe'],
        [['latitude', 'longitude'], 'number'],
        [['city', 'address'], 'string'],
        ];
    }

    
    /**
     * Validates end date of a task
     * * @param string $attribute string being validated
     * @return void
     */
    public function validateDeadline(string $attribute): void
    {
        if ($this->$attribute && strtotime($this->$attribute) < strtotime(date('Y-m-d'))) {
            $this->addError($attribute, 'Дата не может быть раньше текущего дня');
        }
    }

    /**
     * Validates the minimum number of symbols except white-space characters
     * * @param string $attribute string being validated
     * @param array $params any additional parameters from the rule
     * @return void
     */
    public function validateMinNonWhitespace(string $attribute, array $params): void
    {
        $min = $params['min'] ?? 1;
        $text = preg_replace('/\s+/u', '', (string)$this->$attribute);

        if (mb_strlen($text) < $min) {
            $this->addError($attribute, "Минимум {$min} непробельных символов.");
        }
    }

    /**
     * @return array customized attribute labels (name => label)
     */
    public function attributeLabels()
    {
        return [
        'title' => 'Суть работы',
        'description' => 'Подробности задания',
        'category_id' => 'Категория',
        'location_id' => 'Локация',
        'latitude' => 'Широта',
        'longitude' => 'Долгота',
        'cost' => 'Бюджет',
        'date_end' => 'Срок исполнения',
        ];
    }
}
