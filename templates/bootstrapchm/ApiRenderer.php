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
class ApiRenderer extends \yii\apidoc\templates\bootstrap\ApiRenderer
{
    public $layout = '@yii/apidoc/templates/bootstrapchm/layouts/api.php';
    public $indexView = '@yii/apidoc/templates/bootstrapchm/views/index.php';

    // page title ugly hack
    // TODO highly invasive surgery
    protected function renderWithLayout($viewFile, $params)
    {
        $content = parent::renderWithLayout($viewFile, $params);
        $this->pageTitle = $this->getTitle($content);

        return parent::renderWithLayout($viewFile, $params);
    }

    // page title ugly hack
    // TODO highly invasive surgery
    protected function getTitle($content)
    {
        if (preg_match('~<h1>(.*?)</h1>~s', $content, $matches)) {
            $title = str_replace('&para;', '', trim(strip_tags($matches[1])));
        } else {
            $title = null;
        }
        return $title;
    }

    // get external types hack
    // TODO abdominal surgery
    public function getExtTypes($context)
    {
        $types = array_merge($context->classes, $context->interfaces, $context->traits);

        $extTypes = [];
        foreach ($this->extensions as $k => $ext) {
            $extType = $this->filterTypes($types, $ext);
            if (empty($extType)) {
                unset($this->extensions[$k]);
                continue;
            }
            $extTypes[$ext] = $extType;
        }

        return $extTypes;
    }    
}
