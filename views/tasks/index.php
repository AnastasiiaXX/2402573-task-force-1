<?php
/** @var app\models\Task[] $tasks */
?>


<div class="left-column">
    <h3 class="head-main head-task">Новые задания</h3>

    <?php foreach ($tasks as $task): ?>
        <div class="task-card">
            <div class="header-task">
                <a href="#" class="link link--block link--big">
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

</div>
