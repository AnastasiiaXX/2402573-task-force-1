<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var \app\models\User $user */

?>

<main class="main-content container">
    <div class="left-column">
        <h3 class="head-main"><?= Html::encode($user->name) ?></h3>

        <div class="user-card">
            <div class="photo-rate">
                <img class="card-photo" src="<?= Html::encode($user->avatar ?: 'img/man-glasses.png') ?>" 
                     width="191" height="190" alt="Фото пользователя">
                <div class="card-rate">
                    <div class="stars-rating big">
                        <?php 
                        $rating = round($user->getAverageRating() ?? 0);
                        for ($i = 1; $i <= 5; $i++): ?>
                            <span class="<?= $i <= $rating ? 'fill-star' : '' ?>">&nbsp;</span>
                        <?php endfor; ?>
                    </div>
                    <span class="current-rate"><?= number_format($user->getAverageRating() ?? 0, 2) ?></span>
                </div>
            </div>
            <p class="user-description"><?= Html::encode($user->about) ?></p>
        </div>

        <div class="specialization-bio">
            <div class="specialization">
                <p class="head-info">Специализации</p>
                <ul class="special-list">
                    <?php foreach ($user->specialties as $specialty): ?>
                        <li class="special-item">
                            <a href="#" class="link link--regular"><?= Html::encode($specialty->name) ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="bio">
                <p class="head-info">Био</p>
                <p class="bio-info">
                    <span class="country-info"><?= Html::encode($user->location->country ?? 'Россия') ?></span>, 
                    <span class="town-info"><?= Html::encode($user->location->city ?? 'Петербург') ?></span>, 
                    <span class="age-info"><?= $age ?? '-' ?></span>
                </p>
            </div>
        </div>

        <h4 class="head-regular">Отзывы заказчиков</h4>
        <?php foreach ($user->responses as $response): ?>
    <?php $customer = $response->task->employer; ?>
    <div class="response-card">
        <img class="customer-photo" src="<?= Html::encode($customer->avatar ?: 'img/man-coat.png') ?>" 
             width="120" height="127" alt="Фото заказчика">
        <div class="feedback-wrapper">
            <p class="feedback"><?= Html::encode($response->comment) ?></p>
            <p class="task">
                Задание «<a href="<?= Url::to(['tasks/view', 'id' => $response->task_id]) ?>" class="link link--small">
                <?= Html::encode($response->task->title) ?></a>» выполнено
        </div>
    </div>
<?php endforeach; ?>
    </div>

    <div class="right-column">
        <div class="right-card black">
            <h4 class="head-card">Статистика исполнителя</h4>
            <dl class="black-list">
                <dt>Всего заказов</dt>
                <dd><?= $user->getCompletedTasksCount() ?> выполнено, <?= $user->getFailedTasksCount() ?> провалено</dd>
                <dt>Место в рейтинге</dt>
                <dd><?= $user->getRatingPosition() ?> место</dd>
                <dt>Дата регистрации</dt>
                <dd><?= Yii::$app->formatter->asDatetime($user->created_at, 'd MMMM yyyy HH:mm') ?></dd>
                <dt>Статус</dt>
                <dd><?= $user->isAvailable() ? 'Открыт для новых заказов' : 'Закрыт' ?></dd>
            </dl>
        </div>

        <div class="right-card white">
            <h4 class="head-card">Контакты</h4>
            <ul class="enumeration-list">
                <?php if ($user->phone_number): ?>
                    <li class="enumeration-item">
                        <a href="tel:<?= Html::encode($user->phone_number) ?>" class="link link--block link--phone">
                            <?= Html::encode($user->phone_number) ?>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if ($user->email): ?>
                    <li class="enumeration-item">
                        <a href="mailto:<?= Html::encode($user->email) ?>" class="link link--block link--email">
                            <?= Html::encode($user->email) ?>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if ($user->telegram_name): ?>
                    <li class="enumeration-item">
                        <a href="https://t.me/<?= Html::encode($user->telegram_name) ?>" class="link link--block link--tg">
                            @<?= Html::encode($user->telegram_name) ?>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</main>
