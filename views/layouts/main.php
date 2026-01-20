<?php

/** @var yii\web\View $this */
/** @var string $content */

use yii\helpers\Html;
use yii\helpers\Url;


$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
$this->registerCssFile(Yii::getAlias('@web/css/normalize.css'));
$this->registerCssFile(Yii::getAlias('@web/css/style.css'));
$this->registerJsFile(Yii::getAlias('@web/js/main.js'));

$user = Yii::$app->user->identity ?? null;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>

  <title><?= Html::encode($this->title) ?></title>
  <?php $this->head() ?>
</head>

<body>
  <?php $this->beginBody() ?>
  <header class="page-header">
    <nav class="main-nav">
      <a href="<?= Url::to(['site/index']) ?>" class="header-logo">
        <img class="logo-image" src="/img/logotype.png" width="227" height="60" alt="taskforce">
      </a>

      <?php if (!Yii::$app->user->isGuest): ?>
        <div class="nav-wrapper">
          <ul class="nav-list">

            <li class="list-item <?= Yii::$app->controller->id === 'tasks' ? 'list-item--active' : '' ?>">
              <a href="<?= Url::to(['/tasks/index']) ?>" class="link link--nav">Новое</a>
            </li>

            <li class="list-item <?= Yii::$app->controller->id === 'my-tasks' ? 'list-item--active' : '' ?>">
              <a href="<?= Url::to(['/my-tasks/index']) ?>" class="link link--nav">Мои задания</a>
            </li>

            <?php if (Yii::$app->user->can('customer')): ?>
              <li class="list-item">
                <a href="<?= Url::to(['/add-task/index']) ?>" class="link link--nav">Создать задание</a>
              </li>
            <?php endif; ?>

            <?php if (Yii::$app->user->can('worker')): ?>
              <li class="list-item">
                <a href="<?= Url::to(['/my-profile/index']) ?>" class="link link--nav">Настройки</a>
              </li>
            <?php endif; ?>

          </ul>
        </div>
      <?php endif; ?>
    </nav>
    <?php if (!Yii::$app->user->isGuest): ?>
      <div class="user-block">
        <a href="#">
          <img class="user-photo" src="/img/avatars/default-avatar.jpg" width="55" height="55" alt="Аватар">
        </a>
        <div class="user-menu">
          <p class="user-name"><?= Html::encode($user->name) ?></p>
          <div class="popup-head">
            <ul class="popup-menu">
              <?php if (Yii::$app->user->can('worker')): ?>
                <li class="menu-item"><a href="<?= Url::to(['/my-profile/index']) ?>" class="link">Настройки</a></li>
              <?php endif; ?>
              <li class="menu-item"><a href="#" class="link">Связаться с нами</a></li>
              <li class="menu-item">
                <?= Html::beginForm(['/site/logout'], 'post', ['style' => 'display:inline']) ?>
                <button type="submit" class="link" style="background:none;border:none;padding:0;cursor:pointer;">
                  Выход из системы
                </button>
                <?= Html::endForm() ?>
              </li>
            </ul>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </header>

  <main class="main-content container">
    <div class="main-container">
      <?= $content ?>
    </div>
  </main>

  <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>