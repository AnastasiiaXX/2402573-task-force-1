<?php
/** @var yii\web\View $this */
/** @var string $content */

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>TaskForce</title>
    <link rel="stylesheet" href="<?= Yii::getAlias('@web/css/normalize.css') ?>">
    <link rel="stylesheet" href="<?= Yii::getAlias('@web/css/landing.css') ?>">
</head>
<body class="landing">

<?= $content ?>

<div class="overlay"></div>
<script src="<?= Yii::getAlias('@web/js/landing.js') ?>"></script>
</body>
</html>
