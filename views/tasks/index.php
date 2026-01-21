<?php

/** @var app\models\Task[] $tasks */

use yii\widgets\LinkPager;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

?>

<main class="main-content container">
<div class="left-column">
  <h3 class="head-main head-task">Новые задания</h3>

  <?php foreach ($tasks as $task) : ?>
    <div class="task-card">
      <div class="header-task">
        <a href="<?= Url::to(['tasks/view', 'id' => $task->id]) ?>" class="link link--block link--big">
          <?= htmlspecialchars($task->title) ?>
        </a>
        <p class="price price--task">
          <?= $task->cost ? $task->cost . ' ₽' : 'Без цены' ?>
        </p>
      </div>

      <p class="info-text">
        <span class="current-time">
          <?= Yii::$app->formatter->asRelativeTime($task->date_add) ?>
        </span>
      </p>

      <p class="task-text">
        <?= nl2br(htmlspecialchars($task->description)) ?>
      </p>

      <div class="footer-task">
        <p class="info-text town-text">
          <?= $task->location ? $task->location->name : '—' ?>
        </p>
        <p class="info-text category-text">
          <?= $task->category ? $task->category->title : '—' ?>
        </p>
        <a href="<?= Url::to(['tasks/view', 'id' => $task->id]) ?>" class="button button--black">Смотреть задание</a>
      </div>
    </div>
  <?php endforeach; ?>
  <?php if ($pages->pageCount > 1) : ?>
  <div class="pagination-wrapper">
        <?= LinkPager::widget([
        'pagination' => $pages,
        'options' => ['class' => 'pagination-list'],
        'linkContainerOptions' => ['class' => 'pagination-item'],
        'linkOptions' => ['class' => 'link link--page'],
        'activePageCssClass' => 'pagination-item--active',
        'disabledPageCssClass' => 'mark',
    ]) ?>
  </div>
  <?php endif; ?>
  </div>
<div class="right-column">
    <div class="right-card black">
      <div class="search-form">

        <?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => ['tasks/index'],
        'fieldConfig' => [
          'template' => "{input}",
        ],
      ]); ?>

      <h4 class="head-card">Категории</h4>
        <div class="checkbox-wrapper">

          <?= $form->field($filters, 'categories')->checkboxList(
              ArrayHelper::map($categories, 'id', 'title'),
              [
              'item' => function ($index, $label, $name, $checked, $value) {
                return '<label class="control-label">'
                  . Html::checkbox($name, $checked, ['value' => $value])
                  . ' ' . Html::encode($label)
                  . '</label><br>';
              },
              'unselect' => null,
              ]
          )->label(false) ?>
      </div>

      <h4 class="head-card">Дополнительно</h4>
        <?= $form->field($filters, 'notTaken')->checkbox([
          'template' => '<label class="control-label">{input} Без исполнителя</label>',
        ])->label(false) ?>

      <h4 class="head-card">Период</h4>
      <div class="form-group">
        <?= $form->field($filters, 'timePeriod')
          ->dropDownList([
            '' => 'Любой',
            1 => '1 час',
            12 => '12 часов',
            24 => '24 часа',
          ], [
            'id' => 'period-value',
          ])
          ->label(false) ?>
      </div>

     <input type="submit" class="button button--blue" value="Искать">

      <?php ActiveForm::end(); ?>

      </div>
    </div>
  </div>
</main>
