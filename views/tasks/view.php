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
      <?php if (
        !Yii::$app->user->isGuest &&
        $isWorker &&
        !$alreadyResponded &&
        $task->status === Task::STATUS_NEW
      ):
      ?>
        <a href="#" class="button button--blue action-btn" data-action="act_response">Откликнуться на задание</a>
      <?php endif; ?>
      <?php
      if (
        $isWorker &&
        $task->status === Task::STATUS_IN_PROGRESS &&
        (int)$task->worker_id === (int)Yii::$app->user->id
      ):
      ?>
        <a href="#" class="button button--orange action-btn" data-action="refusal">Отказаться от задания</a>
      <?php endif; ?>
      <?php if ($isCustomer &&  $task->status === Task::STATUS_IN_PROGRESS): ?>
        <a href="#" class="button button--pink action-btn" data-action="completion">Завершить задание</a>
      <?php endif; ?>
      <?php if ($task->location): ?>
    <div class="task-map">
      <div id="task-map" style="width: 600px; height: 400px"></div>
      <p class="map-address town">
        <?= Html::encode($task->location->name) ?>
      </p>
    </div>
  <?php endif; ?>
  <?php if (!$isGuest && empty($responses)): ?>
    <h4 class="head-regular">Отклики на задание</h4>
    <p>Пока нет откликов</p>
  <?php else: ?>
    <?php foreach ($responses as $response): ?>
      <div class="response-card">
        <img class="customer-photo" src="img/man-sweater.png" width="146" height="156" alt="Фото заказчиков">
        <div class="feedback-wrapper">
          <a href="<?= Url::to(['users/view', 'id' => $response->worker_id]) ?>"
            class="link link--block link--big">
            <?= htmlspecialchars($response->worker->name) ?>
          </a>
          <div class="response-wrapper">
            <div class="stars-rating small"><span class="fill-star">&nbsp;</span><span class="fill-star">&nbsp;</span><span class="fill-star">&nbsp;</span><span class="fill-star">&nbsp;</span><span>&nbsp;</span></div>
            <p class="reviews">8 отзывов</p>
          </div>
          <p class="response-message">
            <?= htmlspecialchars($response->comment) ?>
          </p>
        </div>
        <div class="feedback-wrapper">
          <p class="info-text">
            <?= Yii::$app->formatter->asRelativeTime($response->date_add) ?>
          </p>
          <p class="price price--small"><?= (int)$response->cost ?> ₽</p>
        </div>
        <?php if (
          $isCustomer &&
          $task->status === Task::STATUS_NEW &&
          $response->status === Response::STATUS_NEW
        ): ?>
          <div class="button-popup">
            <a href="<?= Url::to(['response/accept', 'id' => $response->id]) ?>" class="button button--blue button--small">
              Принять
            </a>
            <a href="<?= Url::to(['response/reject', 'id' => $response->id]) ?>" class="button button--orange button--small">
              Отказать
            </a>
          </div>
        <?php endif ?>
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
            <dt><?= Yii::$app->formatter->asRelativeTime($task->date_add) ?></dt>
            <dt>Срок выполнения</dt>
            <dd><?= Yii::$app->formatter->asRelativeTime($task->date_end) ?></dd>
            <dt>Статус</dt>
            <dd><?= $task->status ?></dd>
          </dl>
        </div>
        <div class="right-card white file-card">
          <h4 class="head-card">Файлы задания</h4>
          <ul class="enumeration-list">
          <?php if (!empty($task->files)): ?>
            <?php foreach ($task->files as $file):
              $filePath = Yii::getAlias('@webroot/uploads/') . $file->path;
              $fileUrl  = Yii::getAlias('@web/uploads/') . $file->path;
            ?>
              <li class="enumeration-item">
                <?php if (file_exists($filePath)): ?>
                  <a href="<?= $fileUrl ?>"
                    class="link link--block link--clip"
                    target="_blank">
                    <?= htmlspecialchars($file->path) ?>
                  </a>
                  <p class="file-size">
                    <?= round(filesize($filePath) / 1024) ?> Кб
                  </p>
                <?php else: ?>
                  <span class="link link--block link--clip text-muted">
                    <?= htmlspecialchars($file->path) ?> (файл отсутствует)
                  </span>
                <?php endif; ?>
              </li>
            <?php endforeach; ?>
          </ul>
           <?php else: ?>
        <p class="text-muted">Файлы не загружены</p>
    <?php endif; ?>
      </div>

<section class="pop-up pop-up--refusal pop-up--close">
  <div class="pop-up--wrapper">
    <h4>Отказ от задания</h4>
    <p class="pop-up-text">
      <b>Внимание!</b><br>
      Вы собираетесь отказаться от выполнения этого задания.<br>
      Это действие плохо скажется на вашем рейтинге и увеличит счетчик проваленных заданий.
    </p>

    <a href="<?= Url::to(['tasks/decline', 'id' => $task->id]) ?>"
      class="button button--pop-up button--orange">Отказаться</a>
    <div class="button-container">
      <button class="button--close" type="button">Закрыть окно</button>
    </div>
  </div>
</section>
<section class="pop-up pop-up--completion pop-up--close">
  <div class="pop-up--wrapper">
    <h4>Завершение задания</h4>
    <p class="pop-up-text">
      Вы собираетесь отметить это задание как выполненное.
      Пожалуйста, оставьте отзыв об исполнителе и отметьте отдельно, если возникли проблемы.
    </p>
    <div class="completion-form pop-up--form regular-form">
      <?php $form = ActiveForm::begin([
        'action' => ['tasks/complete', 'id' => $task->id],
        'method' => 'post'
      ]); ?>
      <div class="form-group">
        <?= $form->field($review, 'text')->textarea() ?>
      </div>
      <p class="completion-head control-label">Оценка работы</p>
      <?= $form->field($review, 'score')
        ->hiddenInput()->label(false) ?>
      <div class="stars-rating big active-stars"></div>
      <?= $form->errorSummary($review) ?>
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
    <h4>Добавление отклика к заданию</h4>
    <p class="pop-up-text">
      Вы собираетесь оставить свой отклик к этому заданию.
      Пожалуйста, укажите стоимость работы и добавьте комментарий, если необходимо.
    </p>
    <div class="addition-form pop-up--form regular-form">
      <?php $form = ActiveForm::begin([
        'action' => ['response/create', 'taskId' => $task->id],
        'method' => 'post'
      ]); ?>
      <div class="form-group">
        <?= $form->field($response, 'comment')->textarea() ?>
      </div>
      <div class="form-group">
        <?= $form->field($response, 'cost')->input('number', ['min' => 1]) ?>
      </div>
      <?= $form->errorSummary($response) ?>
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
  <?php if ($task->location && $task->location->latitude && $task->location->longitude): ?>
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
