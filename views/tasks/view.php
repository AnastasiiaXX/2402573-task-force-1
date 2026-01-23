<?php

use yii\helpers\Url;
use yii\helpers\Html;
use app\models\Task;
use app\models\Response;
use yii\widgets\ActiveForm;

$currentUserId = Yii::$app->user->isGuest ? null : (int)Yii::$app->user->id;

?>
<main class="main-content container">
  <div class="left-column">
    <div class="head-wrapper">
      <h3 class="head-main"><?= htmlspecialchars($task->title) ?></h3>
      <p class="price price--big"><?= $task->cost !== null ? htmlspecialchars($task->cost) . ' ₽' : '—' ?></p>
    </div>

    <p class="task-description">
      <?= htmlspecialchars($task->description) ?>
    </p>

    <div class="task-buttons">
      <?php if (!Yii::$app->user->isGuest && $isWorker && !$alreadyResponded && $task->status === Task::STATUS_NEW) : ?>
        <a href="#" class="button button--blue action-btn" data-action="act_response">Откликнуться на задание</a>
      <?php endif; ?>
      <?php if ($isCustomer && $task->status === Task::STATUS_NEW && (int)$task->employer_id === (int)Yii::$app->user->id) : ?>
        <a href="<?= Url::to(['tasks/cancel', 'id' => $task->id]) ?>" class="button button--yellow">Отменить задание</a>
      <?php endif; ?>
      <?php if ($isWorker && $task->status === Task::STATUS_IN_PROGRESS && (int)$task->worker_id === (int)Yii::$app->user->id) : ?>
        <a href="#" class="button button--orange action-btn" data-action="refusal">Отказаться от задания</a>
      <?php endif; ?>
      <?php if ($isCustomer && $task->status === Task::STATUS_IN_PROGRESS) : ?>
        <a href="#" class="button button--pink action-btn" data-action="completion">Завершить задание</a>
      <?php endif; ?>
    </div> <?php if ($task->location) : ?>
      <div class="task-map">
        <div id="task-map" style="width: 725px; height: 346px"></div>
        <p class="map-address town"><?= Html::encode($task->location->name) ?></p>
      </div>
           <?php endif; ?>

    <h4 class="head-regular">Отклики на задание</h4>
    <?php if (!$isGuest && empty($responses)) : ?>
      <p>Пока нет откликов</p>
    <?php else : ?>
        <?php foreach ($responses as $response) : ?>
            <?php if ($response->worker) : ?>
          <div class="response-card">
            <img class="customer-photo" src="<?= $response->worker->avatar ?
                                                Yii::getAlias('@web/') . $response->worker->avatar :
                                                Yii::getAlias('@web/img/avatars/default-avatar.jpg') ?>" width="146" height="156" alt="Фото исполнителя">
            <div class="feedback-wrapper">
              <a href="<?= Url::to(['users/view', 'id' => $response->worker_id]) ?>" class="link link--block link--big">
                <?= htmlspecialchars($response->worker->name) ?>
              </a>
              <div class="response-wrapper">
                <div class="stars-rating small">
                  <?php
                    $rating = $response->worker->getAverageRating();
                    for ($i = 1; $i <= 5; $i++) : ?>
                    <span class="<?= $i <= $rating ? 'fill-star' : '' ?>">&nbsp;</span>
                    <?php endfor; ?>
                </div>
                <p class="reviews"><?= $response->worker->getReviewsCount() ?> отзывов</p>
              </div>
              <p class="response-message"><?= htmlspecialchars($response->comment) ?></p>
            </div>
            <div class="feedback-wrapper">
              <p class="info-text"><?= Yii::$app->formatter->asRelativeTime($response->date_add) ?></p>
              <p class="price price--small"><?= (int)$response->cost ?> ₽</p>
            </div>
                <?php if ($isCustomer && $task->status === Task::STATUS_NEW && $response->status === Response::STATUS_NEW) : ?>
              <div class="button-popup">
                <a href="<?= Url::to(['response/accept', 'id' => $response->id]) ?>" class="button button--blue button--small">Принять</a>
                <a href="<?= Url::to(['response/reject', 'id' => $response->id]) ?>" class="button button--orange button--small">Отказать</a>
              </div>
                <?php endif ?>
          </div>
            <?php endif; ?>
        <?php endforeach ?>
    <?php endif; ?>
  </div>
  <div class="right-column">
    <div class="right-card black info-card">
      <h4 class="head-card">Информация о задании</h4>
      <dl class="black-list">
        <dt>Категория</dt>
        <dd><?= $task->category->title ?? '' ?></dd>
        <dt>Дата публикации</dt>
        <dd><?= Yii::$app->formatter->asRelativeTime($task->date_add) ?></dd>
        <dt>Срок выполнения</dt>
        <dd><?= $task->date_end ? Yii::$app->formatter->asRelativeTime($task->date_end) : '—' ?></dd>
        <dt>Статус</dt>
        <dd><?= $task->displayStatus() ?></dd>
      </dl>
    </div>
    <div class="right-card white file-card">
      <h4 class="head-card">Файлы задания</h4>
      <ul class="enumeration-list">
        <?php if (!empty($task->files)) : ?>
            <?php foreach ($task->files as $file) :
                $filePath = Yii::getAlias('@webroot/uploads/') . $file->path;
                $fileUrl  = Url::to('/uploads/' . $file->path);
                ?>
            <li class="enumeration-item">
              <a href="<?= $fileUrl ?>" class="link link--block link--clip" target="_blank"><?= htmlspecialchars($file->path) ?></a>
              <p class="file-size"><?= file_exists($filePath) ? round(filesize($filePath) / 1024) . ' Кб' : '' ?></p>
            </li>
            <?php endforeach; ?>
        <?php else : ?>
          <p class="text-muted">Файлы не загружены</p>
        <?php endif; ?>
      </ul>
    </div>
  </div>

  <section class="pop-up pop-up--refusal pop-up--close">
    <div class="pop-up--wrapper">
      <h4>Отказ от задания</h4>
      <p class="pop-up-text">Вы собираетесь отказаться от выполнения этого задания.</p>
      <a href="<?= Url::to(['tasks/decline', 'id' => $task->id]) ?>" class="button button--pop-up button--orange">Отказаться</a>
      <div class="button-container">
        <button class="button--close" type="button">Закрыть окно</button>
      </div>
    </div>
  </section>

  <section class="pop-up pop-up--completion pop-up--close">
    <div class="pop-up--wrapper">
      <h4>Завершение задания</h4>
      <div class="completion-form pop-up--form regular-form">
        <?php $form = ActiveForm::begin(['action' => ['tasks/complete', 'id' => $task->id]]); ?>
        <?= $form->field($review, 'text')->textarea() ?>
          <?= $form->field($review, 'score')->hiddenInput(['id' => 'review-score'])->label(false) ?>
          <div class="stars-rating big active-stars">
            <span data-value="1">&nbsp;</span>
            <span data-value="2">&nbsp;</span>
            <span data-value="3">&nbsp;</span>
            <span data-value="4">&nbsp;</span>
            <span data-value="5">&nbsp;</span>
          </div>
        <button type="submit" class="button button--pop-up button--blue">Завершить</button>
        <?php ActiveForm::end(); ?>
      </div>
      <div class="button-container">
        <button class="button--close" type="button">Закрыть окно</button>
      </div>
    </div>
  </section>

  <section class="pop-up pop-up--act_response pop-up--close">
    <div class="pop-up--wrapper">
      <h4>Добавление отклика</h4>
      <div class="addition-form pop-up--form regular-form">
        <?php $form = ActiveForm::begin(['action' => ['response/create', 'taskId' => $task->id],
        'enableClientValidation' => true,
        'validateOnType' => true,
        ]); ?>
        <?= $form->field($response, 'comment')->textarea() ?>
        <?= $form->field($response, 'cost')->input('number') ?>
        <button type="submit" class="button button--pop-up button--blue">Отправить</button>
        <?php ActiveForm::end(); ?>
      </div>
      <div class="button-container">
        <button class="button--close" type="button">Закрыть окно</button>
      </div>
    </div>
  </section>
</main>

<script src="https://api-maps.yandex.ru/2.1/?apikey=<?= Yii::$app->params['yandexGeocoderApiKey'] ?>&lang=ru_RU" type="text/javascript"></script>
<script type="text/javascript">
  <?php if ($task->location && $task->location->latitude && $task->location->longitude) : ?>
    ymaps.ready(init);

    function init() {
      var myMap = new ymaps.Map("task-map", {
        center: [<?= $task->location->latitude ?>, <?= $task->location->longitude ?>],
        zoom: 15
      });
      myMap.geoObjects.add(new ymaps.Placemark(
        [<?= $task->location->latitude ?>, <?= $task->location->longitude ?>], {
          balloonContent: '<?= addslashes($task->location->name) ?>'
        }
      ));
    }
  <?php endif; ?>

</script>

<?php
$js = <<<JS
$('.active-stars span').on('click', function() {
    var score = $(this).data('value');
    var input = $('#review-score');
    var form = input.closest('form');
    
    input.val(score);
    
    $('.active-stars span').removeClass('fill-star');
    $(this).prevAll().addBack().addClass('fill-star');

    form.yiiActiveForm('validateAttribute', 'review-score');
});
JS;
$this->registerJs($js);
?>
