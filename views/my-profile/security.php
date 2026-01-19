<?php 
use yii\widgets\Menu;
use yii\widgets\ActiveForm;

?>

<main class="main-content main-content--left container">
    <div class="left-menu left-menu--edit">
        <h3 class="head-main head-task">Настройки</h3>
        <?= Menu::widget([
            'options' => [
                'class' => 'side-menu-list',
            ],
            'items' => [
                ['label' => 'Мой профиль', 'url' => ['/my-profile/index']],
                ['label' => 'Безопасность', 'url' => ['/my-profile/security']],
            ],
            'itemOptions' => [
                'class' => 'side-menu-item',
            ],
            'linkTemplate' => '<a href="{url}" class="link link--nav">{label}</a>',
            'activeCssClass' => 'side-menu-item--active',
            'activateItems' => true,
            'activateParents' => false,
        ]); ?>
    </div>
    <div class="my-profile-form">
        <?php $form = ActiveForm::begin([
            'id' => 'secure-form',
            'method' => 'post',
            'fieldConfig' => [
                'template' => "{label}{input}\n{error}",
                'errorOptions' => ['class' => 'help-block']
            ],
        ]); ?>
        <h3 class="head-main head-regular">Безопасность</h3>
        <?= $form->field($passwordForm, 'currentPassword')->passwordInput(); ?>
        <?= $form->field($passwordForm, 'newPassword')->passwordInput(); ?>
        <?= $form->field($passwordForm, 'newPasswordRepeat')->passwordInput(); ?>

        <input type="submit" class="button button--blue" value="Сохранить">
        <?php ActiveForm::end(); ?>
    </div>
</main>