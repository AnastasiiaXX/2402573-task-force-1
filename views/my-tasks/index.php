<?php
use yii\helpers\Url;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var app\models\Task[] $tasks
 * @var string $role
 * @var string|null $status
 */
?>

<main class="main-content container">
    <div class="left-menu">
        <div class="fixedMenu">
            <h3 class="head-main head-task">Мои задания</h3>

            <ul class="side-menu-list">
                <?php if ($role === 'customer'): ?>
                    <li class="side-menu-item <?= $status === 'new' ? 'side-menu-item--active' : '' ?>">
                        <a href="<?= Url::to(['my-tasks/index', 'status' => 'new']) ?>" class="link link--nav">
                            Новые
                        </a>
                    </li>
                    <li class="side-menu-item <?= $status === 'in_progress' ? 'side-menu-item--active' : '' ?>">
                        <a href="<?= Url::to(['my-tasks/index', 'status' => 'in_progress']) ?>" class="link link--nav">
                            В процессе
                        </a>
                    </li>
                    <li class="side-menu-item <?= $status === 'closed' ? 'side-menu-item--active' : '' ?>">
                        <a href="<?= Url::to(['my-tasks/index', 'status' => 'closed']) ?>" class="link link--nav">
                            Закрытые
                        </a>
                    </li>
                <?php else: ?>
                    <li class="side-menu-item <?= $status === 'in_progress' ? 'side-menu-item--active' : '' ?>">
                        <a href="<?= Url::to(['my-tasks/index', 'status' => 'in_progress']) ?>" class="link link--nav">
                            В процессе
                        </a>
                    </li>
                    <li class="side-menu-item <?= $status === 'overdue' ? 'side-menu-item--active' : '' ?>">
                        <a href="<?= Url::to(['my-tasks/index', 'status' => 'overdue']) ?>" class="link link--nav">
                            Просроченные
                        </a>
                    </li>
                    <li class="side-menu-item <?= $status === 'closed' ? 'side-menu-item--active' : '' ?>">
                        <a href="<?= Url::to(['my-tasks/index', 'status' => 'closed']) ?>" class="link link--nav">
                            Закрытые
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="left-column left-column--task">
        <h3 class="head-main head-regular">
            <?php if ($role === 'customer'): ?>
                <?= match ($status) {
                    'new' => 'Новые задания',
                    'in_progress' => 'Задания в процессе',
                    'closed' => 'Закрытые задания',
                    default => 'Все задания',
                } ?>
            <?php else: ?>
                <?= match ($status) {
                    'in_progress' => 'Задания в процессе',
                    'overdue' => 'Просроченные задания',
                    'closed' => 'Закрытые задания',
                    default => 'Мои задания',
                } ?>
            <?php endif; ?>
        </h3>

        <?php if ($tasks): ?>
            <?php foreach ($tasks as $task): ?>
                <div class="task-card">
                    <div class="header-task">
                        <a href="<?= Url::to(['tasks/view', 'id' => $task->id]) ?>"
                           class="link link--block link--big">
                            <?= Html::encode($task->title) ?>
                        </a>

                        <?php if ($task->cost !== null): ?>
                            <p class="price price--task"><?= $task->cost ?> ₽</p>
                        <?php endif; ?>
                    </div>

                    <p class="info-text" title="<?= $task->date_add ?>">
                        <span class="current-time">
                            <?= Yii::$app->formatter->asRelativeTime($task->date_add) ?>
                        </span>
                    </p>

                    <p class="task-text">
                        <?= Html::encode($task->description) ?>
                    </p>

                    <div class="footer-task">
                        <?php if ($task->location): ?>
                            <p class="info-text town-text"><?= Html::encode($task->location->name) ?></p>
                        <?php endif; ?>

                        <?php if ($task->category): ?>
                            <p class="info-text category-text"><?= Html::encode($task->category->title) ?></p>
                        <?php endif; ?>

                        <a href="<?= Url::to(['tasks/view', 'id' => $task->id]) ?>"
                           class="button button--black">
                            Смотреть задание
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="info-text">Заданий в этом разделе нет</p>
        <?php endif; ?>
    </div>
</main>
