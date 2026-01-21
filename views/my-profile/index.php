<?php

use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

?>

<main class="main-content main-content--left container">
<div class="left-menu left-menu--edit">
  <h3 class="head-main head-task">Настройки</h3>
  <ul class="side-menu-list">
    <li class="side-menu-item side-menu-item--active">
      <a href="<?= Url::to(['/my-profile/index']) ?>" class="link link--nav">Мой профиль</a>
    </li>
    <li class="side-menu-item">
      <a href="<?= Url::to(['/my-profile/security']) ?>" class="link link--nav">Безопасность</a>
    </li>
  </ul>
</div>

<?php $form = ActiveForm::begin([
  'options' => [
    'enctype' => 'multipart/form-data',
    'class' => 'my-profile-form',
  ],
]); ?>

<h3 class="head-main head-regular">Мой профиль</h3>

<div class="photo-editing">
  <div>
    <p class="form-label">Аватар</p>
    <img
      class="avatar-preview"
      src="/<?= $model->avatar ?: 'img/avatars/default-avatar.jpg' ?>"
      width="83"
      height="83">
  </div>

  <?= $form->field($model, 'avatarFile', [
    'template' => '{input}',
  ])->fileInput([
            'id' => 'button-input',
            'hidden' => true,
  ]) ?>

  <label for="button-input" class="button button--black">
    Сменить аватар
  </label>
</div>

  <?= $form->field($model, 'name')->textInput([
    'id' => 'profile-name',
  ])->label('Ваше имя') ?>


<div class="half-wrapper">
    <?= $form->field($model, 'email')->textInput([
      'id' => 'profile-email',
    ])->label('Email') ?>


    <?= $form->field($model, 'birthday')->input('date', [
      'id' => 'profile-date',
      'value' => $model->birthday
        ? date('Y-m-d', strtotime($model->birthday))
        : null,
    ])->label('День рождения') ?>
</div>

<div class="half-wrapper">
    <?= $form->field($model, 'phone_number')->textInput([
      'id' => 'profile-phone',
    ])->label('Номер телефона') ?>

    <?= $form->field($model, 'telegram_name')->textInput([
      'id' => 'profile-tg',
    ])->label('Telegram') ?>
</div>

  <?= $form->field($model, 'about')->textarea([
    'id' => 'profile-info',
  ])->label('Информация о себе') ?>


<div class="form-group">
  <p class="form-label">Выбор специализаций</p>

  <?= $form->field($model, 'category_ids', [
    'template' => '{input}',
  ])->checkboxList(
      ArrayHelper::map($categories, 'id', 'title'),
      [
      'class' => 'checkbox-profile',
      'item' => function ($index, $label, $name, $checked, $value) {
        return '<label class="control-label">'
          . Html::checkbox($name, $checked, ['value' => $value])
          . ' ' . Html::encode($label)
          . '</label>';
      }
      ]
  ) ?>
</div>

<input type="submit" class="button button--blue" value="Сохранить">

<?php ActiveForm::end(); ?>
</main>
