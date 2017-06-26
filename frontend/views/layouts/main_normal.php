<?php
use yii\helpers\Html;

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
    <meta name="format-detection" content="telephone=no" />
    <meta charset="<?= Yii::$app->charset ?>">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body >
<style>
    html{overflow: auto; overflow-x:hidden}
    body{margin: 0px;}
</style>
<?php $this->beginBody() ?>

<?= $content ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
