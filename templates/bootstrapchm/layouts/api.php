<?php

use yii\apidocchm\templates\bootstrapchm\ApiRenderer;
use yii\apidocchm\templates\bootstrap\SideNavWidget;
use yii\helpers\StringHelper;

/**
 * @var yii\web\View $this
 * @var array $types
 * @var string $content
 */

/** @var ApiRenderer $renderer */
$renderer = $this->context;

$this->beginContent('@yii/apidocchm/templates/bootstrapchm/layouts/main.php'); ?>

<div class="row">
    <div class="col-md-9 api-content" role="main">
        <?= $content ?>
    </div>
</div>

<script type="text/javascript">
    /*<![CDATA[*/
    $("a.toggle").on('click', function () {
        var $this = $(this);
        if ($this.hasClass('properties-hidden')) {
            $this.text($this.text().replace(/Show/,'Hide'));
            $this.parents(".summary").find(".inherited").show();
            $this.removeClass('properties-hidden');
        } else {
            $this.text($this.text().replace(/Hide/,'Show'));
            $this.parents(".summary").find(".inherited").hide();
            $this.addClass('properties-hidden');
        }

        return false;
    });
    /*
     $(".sourceCode a.show").toggle(function () {
     $(this).text($(this).text().replace(/show/,'hide'));
     $(this).parents(".sourceCode").find("div.code").show();
     },function () {
     $(this).text($(this).text().replace(/hide/,'show'));
     $(this).parents(".sourceCode").find("div.code").hide();
     });
     $("a.sourceLink").click(function () {
     $(this).attr('target','_blank');
     });
     */
    /*]]>*/
</script>
<?php $this->endContent(); ?>
