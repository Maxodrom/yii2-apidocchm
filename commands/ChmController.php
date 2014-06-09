<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\apidocchm\commands;

use yii\apidocchm\models\Context;
use yii\apidocchm\renderers\GuideRenderer;
use yii\helpers\Console;
use yii\helpers\FileHelper;
use yii\console\Controller;
use Yii;

use yii\helpers\ArrayHelper;
use yii\helpers\HTML;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;

use yii\apidocchm\helpers\IndexFileAnalyzer;
use yii\apidocchm\components\BaseController;

class ChmController extends BaseController
{
    const GUIDE_PREFIX = 'guide-';

    public $layout = false;

    /**
     * @var string template to use for rendering
     */
    public $template = 'bootstrapchm';

    protected $renderer;

    /**
     * @var array project files list
     */
    protected $files = [];

    protected $indexItems = [];

    /**
     * Renders API documentation files
     * @param string $guideSourceDir
     * @param string $htmlSourceDir
     * @return int
     */
    public function actionIndex($htmlSourceDir, $guideSourceDir = '')
    {
        // only for manupulating class data, template is hardcoded
        $this->renderer = $this->findRenderer();

        $htmlSourceDir = rtrim(Yii::getAlias($htmlSourceDir), '\\/');

        if (!$guideSourceDir)
        {
            $guideSourceDir = rtrim(Yii::getAlias('@yii/../docs/guide', '\\/'));
        }

        $indexAnalyzer = new IndexFileAnalyzer();
        $guideChapters = $indexAnalyzer->analyze(file_get_contents($guideSourceDir . '/' . 'README.md'));

        // TODO error handling

        $this->addFile(static::GUIDE_PREFIX . 'README.html');
        $guideChapters = $this->generateGuideChapters($guideChapters, $guideSourceDir);

        // load context from cache
        $context = $this->loadContext($htmlSourceDir);

        // TODO error handling

        // core classes
        $types = array_merge($context->classes, $context->interfaces, $context->traits);
        $coreTypes = $this->renderer->getNavTypes($context->getType('Yii'), $types);
        $coreChapters = $this->generateAPIChapters($coreTypes);

        // extension classes quick and dirty
        $extensionsTypes = $this->renderer->getExtTypes($context);

        foreach ($extensionsTypes as $ext => $types) {
            $extensionsChapters[] = [
                'headline' => $ext,
                'file' => "ext-{$ext}-index.html",
                'content' => $this->generateAPIChapters($types)
            ];
            $this->addFile("ext-{$ext}-index.html");
        }

        $chapters = [
            ['headline' => 'Guide', 'file' => static::GUIDE_PREFIX . 'README.html', 'content' => $guideChapters],
            ['headline' => 'Core API', 'file' => 'index.html', 'content' => $coreChapters],
            ['headline' => 'Extensions API', 'content' => $extensionsChapters],
        ];

        $tocNodeCallback = function ($item) use (&$tocNodeCallback) {
            $params = HTML::beginTag('param', ['name' => 'Name', value => $item['headline']]);
            // no param omitting in single-level index entries
            if ($item['file'] || ($item['index'] && !$item['content']))
            {
                $params .= HTML::beginTag('param', ['name' => 'Local', value => $item['file']]);
            }
            // duplicate second-level index entries to the top one, both usability and compatibility
            if ($item['index'] && $item['content'])
            {
                foreach ($item['content'] as $nestedItem)
                {
                    $params .= HTML::beginTag('param', ['name' => 'Name', value => $item['headline']]);
                    $params .= HTML::beginTag('param', ['name' => 'Local', value => $nestedItem['file']]);
                }
            }
            $nested = is_array($item['content']) ? "\n" . HTML::ul($item['content'], ['item' => $tocNodeCallback]) . "\n" : '';

            return HTML::tag('li', HTML::tag('object', $params, ['type' => 'text/sitemap']) . $nested);
        };

        $toc = HTML::ul($chapters, ['item' => $tocNodeCallback]);
        $index = HTML::ul($this->generateIndex(), ['item' => $tocNodeCallback]);

        $this->stdout("Writing CHM project files...");

        // chm project goes into the same directory as html source to avoid path issues
        file_put_contents(
            $htmlSourceDir . '/_yii2-chm_toc.hhc',
            $this->render('toc', ['toc' => $toc])
            // $this->render('toc', ['toc' => VarDumper::dumpAsString($chapters)])
        );
        file_put_contents(
            $htmlSourceDir . '/_yii2-chm_index.hhk',
            // $this->render('toc', ['toc' => $toc])
            $this->render('toc', ['toc' => $index])
        );
        file_put_contents(
            $htmlSourceDir . '/_yii2-chm_project.hhp',
            $this->render('project', ['files' => $this->files])
        );

        file_put_contents(
            $htmlSourceDir . '/_yii2-chm_stop.hhp',
            $this->render('stop')
        );

        $this->stdout('done.' . PHP_EOL, Console::FG_GREEN);
    }
        
    /**
     * Adds a file to project files list
     * @param string $file
     * @param string $path
     */
    protected function addFile($file) {
        $file = basename($file);
        if (!in_array($file, $this->files))
        {
            $this->files[] = $file;
        }
    }

    /**
     * Adds an item to index
     * @param string $file
     * @param string $title
     * @param mixed $remark
     */
    protected function addIndexItem($file, $title, $remark = null) {
        $file = basename($file);
        {
            if (!isset($this->indexItems[$title]))
            {
                $this->indexItems[$title] = [];
            }
            $this->indexItems[$title][$file] = $remark;
        }
    }

    /**
     * Generate flat and nested index entries
     * @return array
     */
    protected function generateIndex() {
        $chapters = [];

        ksort($this->indexItems);
        foreach ($this->indexItems as $title => $item)
        {
            if (count($item) > 1)
            {
                $subchapters = [];
                foreach ($item as $file => $remark)
                {
                    $subchapters[] = [
                        'headline' => !empty($remark) ? $remark : $title,
                        'file' => $file,
                    ];
                }
                $chapters[] = [
                    'headline' => $title,
                    'content' => $subchapters,
                    'index' => true
                ];
            }
            else
            {
                $chapters[] = [
                    'headline' => $title,
                    'file' => array_keys($item)[0],
                    'index' => true
                ];                
            }
        }

        return $chapters;
    }

    /**
     * Filters out WIP chapter links
     * @return array
     */
    protected function generateGuideChapters($guideChapters, $guideSourceDir) {
        foreach ($guideChapters as &$h1)
        {
            if (isset($h1['file']))
            {
                if (file_exists($guideSourceDir . '/' . $h1['file']))
                {
                    $h1['file'] = static::GUIDE_PREFIX . basename($h1['file'], '.md') . '.html';
                    $this->addFile($h1['file']);
                }
                else
                {
                    unset($h1['file']);
                }
            }

            if (count($h1['content']))
            {
                foreach ($h1['content'] as $i => &$h2)
                {
                    if (isset($h2['file']) && file_exists($guideSourceDir . '/' . $h2['file']))
                    {
                        $h2['file'] = static::GUIDE_PREFIX . basename($h2['file'], '.md') . '.html';
                        $this->addFile($h2['file']);
                    }
                    else
                    {
                        unset($h1['content'][$i]);
                    }   
                }
            }
        }

        return $guideChapters;
    }

    /**
     * @inheritdoc
     * @return array
     */
    protected function generateAPIChapters($types) {
        $chapters = [];

        ksort($types);
        foreach ($types as $i => $class) {
            $namespace = $class->namespace;
            if (empty($namespace)) {
                $namespace = '';
            }
            if (!isset($chapters[$namespace])) {
                $chapters[$namespace] = [
                    'headline' => $namespace,
                    'content' => [],
                ];
            }
            $file = $this->renderer->generateApiUrl($class->name);
            $name = StringHelper::basename($class->name);
            $chapters[$namespace]['content'][] = [
                'headline' => $name,
                'file' => $file,
            ];

            $this->addFile($file);

            // add both short and namespaced class names
            $this->addIndexItem($file, $name, $class->name);
            $this->addIndexItem($file, $class->name);

            foreach (array_keys($class->methods) as $method)
            {
                $this->addIndexItem($file . "#{$method}()-detail", $method . '()', $class->name);                
            }

        }

        // unwrap non-namespaced classes
        if ($chapters[''] && count($chapters['']['content']))
        {
            $chapters = array_merge($chapters['']['content'], $chapters);
            unset($chapters['']);
        }

        return $chapters;
    }

    /**
     * abstract implementation
     */
    protected function findFiles($path, $except = [])
    {
    }

    /**
     * @inheritdoc
     * @return ApiRenderer
     */
    protected function findRenderer($template = 'bootstrapchm')
    {
        $rendererClass = 'yii\\apidocchm\\templates\\' . $template . '\\ApiRenderer';
        if (!class_exists($rendererClass)) {
            $this->stderr('Renderer not found.' . PHP_EOL);

            return false;
        }

        return new $rendererClass();
    }
}
