<?php
/** @var yii\web\ForbiddenHttpException $exception */
use yii\helpers\Html;
use yii\helpers\Url;
?>
<main class="main-content container">
    <div style="display: flex; flex-direction: column; gap: 20px;">
        <h1>Ошибка: <?= Html::encode($exception->getMessage()) ?></h1>
        <div>
            <a href="<?= Url::to(['tasks/index']) ?>" class="link--block" style="display: inline-block;">
                Вернуться к списку задач
            </a>
        </div>
    </div>
</main>