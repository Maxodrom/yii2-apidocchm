<?php

use yii\apidocchm\templates\bootstrap\SideNavWidget;

/**
 * @var yii\web\View $this
 * @var string $content
 */

$this->beginContent('@yii/apidocchm/templates/bootstrapchm/layouts/main.php'); ?>

<div class="row">
    <div class="col-md-9 guide-content" role="main">
        <?= $content ?>
    </div>
</div>

<?php $this->endContent(); ?>
