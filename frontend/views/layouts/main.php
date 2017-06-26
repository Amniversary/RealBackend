<?php
/* @var $this \yii\web\View */
/* @var $content string */
use yii\helpers\Html;

//AppAsset::register($this);
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
    <style>
        html{overflow-x:hidden;}
        .wishviewmain
        {
            margin: 0px auto;
            overflow: hidden;
        }
        body
        {
            margin: 0px;
        }
    </style>
</head>
<body>

<div class="wishviewmain">
<?php $this->beginBody() ?>

<?= $content ?>

<?php $this->endBody() ?>
</div>
</body>
</html>
<?php $this->endPage() ?>
