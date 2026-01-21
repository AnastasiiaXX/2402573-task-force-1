<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Task;

/** @var \app\models\User $user */


function get_noun_plural($number, $one, $two, $five)
{
    $n = abs($number) % 100;
    $n1 = $n % 10;
    if ($n > 10 && $n < 20) {
        return $five;
    }
    if ($n1 > 1 && $n1 < 5) {
        return $two;
    }
    if ($n1 == 1) {
        return $one;
    }
    return $five;
}

$age = $user->birthday ? (new \DateTime())->diff(new \DateTime($user->birthday))->y : null;

$ageString = $age ? $age . ' ' . get_noun_plural($age, 'год', 'года', 'лет') : '-';

$rating = $user->getAverageRating() ?? 0;
$reviewsCount = count($user->responses);

// Проверка: был ли текущий пользователь заказчиком у этого исполнителя
$isLinkedCustomer = Task::find()
  ->where(['employer_id' => Yii::$app->user->id, 'worker_id' => $user->id])
  ->exists();

/** * Логика видимости контактов по ТЗ:
 * Блок виден всем, ЕСЛИ не включена опция "скрывать".
 * Такая опция отсутствует в верстке и задании "Настройка профиля".
 * Если опция включена — виден только тем заказчикам, кто работал с исполнителем.
 * Так как поля в БД нет, считаем, что опция по умолчанию выключена (контакты видны всем).
 */
$showContacts = true;

// Пример того, как это выглядело бы с полем в БД:
// if ($user->show_contacts_only_customer && !$isLinkedCustomer) { $showContacts = false; }

?>

<main class="main-content container">
  <div class="left-column">
    <h3 class="head-main"><?= Html::encode($user->name) ?></h3>

    <div class="user-card">
      <div class="photo-rate">
        <img class="card-photo" src="<?= $user->avatar ? Url::to('/' . $user->avatar) : Url::to('/img/avatars/default-avatar.jpg') ?>"
          width="191" height="190" alt="Фото пользователя">

        <?php if ($reviewsCount > 0) : ?>
          <div class="card-rate">
            <div class="stars-rating big">
              <?php for ($i = 1; $i <= 5; $i++) : ?>
                <span class="<?= $i <= round($rating) ? 'fill-star' : '' ?>">&nbsp;</span>
              <?php endfor; ?>
            </div>
            <span class="current-rate"><?= number_format($rating, 2) ?></span>
          </div>
        <?php endif; ?>
      </div>
      <p class="user-description"><?= Html::encode($user->about) ?></p>
    </div>

    <div class="specialization-bio">
      <div class="specialization">
        <p class="head-info">Специализации</p>
        <ul class="special-list">
          <?php foreach ($user->categories as $category) : ?>
            <li class="special-item">
              <a href="<?= Url::to(['tasks/index', 'TaskFilter[categories][]' => $category->id]) ?>" class="link link--regular">
                <?= Html::encode($category->title) ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="bio">
        <p class="head-info">Био</p>
        <p class="bio-info">
          <span class="country-info"><?= Html::encode($user->location->country ?? 'Россия') ?></span>,
          <span class="town-info"><?= Html::encode($user->location->city ?? 'Петербург') ?></span>,
          <span class="age-info"><?= $ageString ?></span>
        </p>
      </div>
    </div>
    <?php if ($user->reviews) : ?>
    <h4 class="head-regular">Отзывы заказчиков</h4>
        <?php foreach ($user->reviews as $review) : ?>
        <div class="response-card">
            <img class="customer-photo" src="<?= Yii::getAlias('@web') . ($review->employer->avatar ?: '/img/avatars/default-avatar.jpg') ?>"
                 width="120" height="127" alt="Фото заказчика">
            <div class="feedback-wrapper">
                <p class="feedback"><?= Html::encode($review->text) ?></p>
                <p class="task">
                    Задание «<a href="<?= Url::to(['tasks/view', 'id' => $review->task_id]) ?>" class="link link--small">
                        <?= Html::encode($review->task->title) ?></a>» выполнено
                </p>
            </div>
            <div class="feedback-wrapper">
                <div class="stars-rating small">
                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                        <span class="<?= $i <= $review->score ? 'fill-star' : '' ?>">&nbsp;</span>
                    <?php endfor; ?>
                </div>
                <p class="info-text"><?= Yii::$app->formatter->asRelativeTime($review->date_add) ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
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
        <dd><?= $user->isAvailable() ? 'Открыт для новых заказов' : 'Занят заказом' ?></dd>
      </dl>
    </div>

    <?php if ($showContacts) : ?>
      <div class="right-card white">
        <h4 class="head-card">Контакты</h4>
        <ul class="enumeration-list">
          <?php if ($user->phone_number) : ?>
            <li class="enumeration-item">
              <a href="tel:<?= Html::encode($user->phone_number) ?>" class="link link--block link--phone">
                <?= Html::encode($user->phone_number) ?>
              </a>
            </li>
          <?php endif; ?>
          <?php if ($user->email) : ?>
            <li class="enumeration-item">
              <a href="mailto:<?= Html::encode($user->email) ?>" class="link link--block link--email">
                <?= Html::encode($user->email) ?>
              </a>
            </li>
          <?php endif; ?>
          <?php if ($user->telegram_name) : ?>
            <li class="enumeration-item">
              <a href="https://t.me/<?= Html::encode($user->telegram_name) ?>" class="link link--block link--tg">
                @<?= Html::encode($user->telegram_name) ?>
              </a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    <?php endif; ?>
  </div>
</main>
