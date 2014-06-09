<?php

use yii\apidoc\renderers\BaseRenderer;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 */

\yii\apidoc\templates\bootstrapchm\assets\AssetBundle::register($this);

$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="language" content="en" />
    <?php $this->head() ?>
    <title><?= Html::encode($this->context->pageTitle) ?></title>
</head>
<body>

<?php $this->beginBody() ?>
<div class="wrap">

    <?= $content ?>

</div>

<footer class="footer">
    <?php /* <p class="pull-left">&copy; My Company <?= date('Y') ?></p> */ ?>
    <p class="pull-right"><small>Page generated on <?= date('r') ?></small></p>
    <?= Yii::powered() ?>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
