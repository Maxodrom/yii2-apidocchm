<?php

use yii\apidocchm\models\ClassDoc;
use yii\apidocchm\models\InterfaceDoc;
use yii\apidocchm\models\TraitDoc;

/* @var $types ClassDoc[]|InterfaceDoc[]|TraitDoc[] */
/* @var $this yii\web\View */
/* @var $renderer \yii\apidocchm\templates\html\ApiRenderer */

$renderer = $this->context;

if (isset($readme)) {
    echo \yii\apidocchm\helpers\ApiMarkdown::process($readme);
}

?><h1>Class Reference</h1>

<table class="summaryTable docIndex table table-bordered table-striped table-hover">
    <colgroup>
        <col class="col-package" />
        <col class="col-class" />
        <col class="col-description" />
    </colgroup>
    <tr>
        <th>Class</th>
        <th>Description</th>
    </tr>
<?php
ksort($types);
foreach ($types as $i => $class):
?>
    <tr>
        <td><?= $renderer->createTypeLink($class, $class, $class->name) ?></td>
        <td><?= \yii\apidocchm\helpers\ApiMarkdown::process($class->shortDescription, $class, true) ?></td>
    </tr>
<?php endforeach; ?>
</table>
