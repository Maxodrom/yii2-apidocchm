<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\apidoc\templates\bootstrapchm;

use Yii;
use yii\apidoc\helpers\ApiIndexer;
use yii\helpers\Console;
use yii\helpers\FileHelper;

/**
 *
 * @author Carsten Brandt <mail@cebe.cc>
 * @since 2.0
 */
class GuideRenderer extends \yii\apidoc\templates\bootstrap\GuideRenderer
{
    public $layout = '@yii/apidoc/templates/bootstrapchm/layouts/guide.php';

    // page title ugly hack
    // TODO highly invasive surgery
    protected function fixMarkdownLinks($content)
    {
    	$content = parent::fixMarkdownLinks($content);

		$this->pageTitle = $this->getTitle($content);

        return $content;
    }

    // page title ugly hack
    // TODO highly invasive surgery
    protected function getTitle($contents)
    {
        if (preg_match('~<h1>(.*?)</h1>~s', $contents, $matches)) {
            $title = str_replace('&para;', '', trim(strip_tags($matches[1])));
        } else {
            $title = null;
        }
        return $title;
    }
}
