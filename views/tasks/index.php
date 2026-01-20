<?php

/** @var app\models\Task[] $tasks */

use yii\widgets\LinkPager;
use yii\helpers\Url;
?>
<main class="main-content container">
<div class="left-column">
  <h3 class="head-main head-task">Новые задания</h3>

  <?php foreach ($tasks as $task): ?>
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
        <a href="#" class="button button--black">Смотреть задание</a>
      </div>
    </div>
  <?php endforeach; ?>
  <?php if ($pages->pageCount > 1): ?>
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
        <form method="get" action="/index.php">
          <input type="hidden" name="r" value="tasks/index">

          <h4 class="head-card">Категории</h4>
          <div class="form-group">
            <div class="checkbox-wrapper">
              <?php foreach ($categories as $category): ?>
                <label class="control-label" for="category-<?= $category->id ?>">
                  <input type="checkbox"
                    name="categories[]"
                    id="category-<?= $category->id ?>"
                    value="<?= $category->id ?>"
                  <?= in_array($category->id, $filters->categories ?? []) ? 'checked' : '' ?>>
                  <?= $category->title ?>
                </label>
              <?php endforeach; ?>
            </div>
          </div>

          <h4 class="head-card">Дополнительно</h4>
          <div class="form-group">
            <label class="control-label" for="without-performer">
              <input id="without-performer"
                type="checkbox"
                name="notTaken"
                value="1"
                <?= $filters->notTaken ? "checked" : "" ?>>
              Без исполнителя
            </label>
          </div>

          <h4 class="head-card">Период</h4>
          <div class="form-group">
            <label for="period-value"></label>
            <select name="timePeriod">
              <option value="">Любой</option>
              <option value="1" <?= $filters->timePeriod == 1 ? 'selected' : '' ?>>1 час</option>
              <option value="12" <?= $filters->timePeriod == 12 ? 'selected' : '' ?>>12 часов</option>
              <option value="24" <?= $filters->timePeriod == 24 ? 'selected' : '' ?>>24 часа</option>
            </select>
          </div>
          <input type="submit" class="button button--blue" value="Искать">
        </form>
      </div>
    </div>
  </div>
</main>