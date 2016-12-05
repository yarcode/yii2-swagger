<?php

namespace YarCode\Yii2\Swagger;

use Yii;
use yii\base\InvalidConfigException;
use yii\web\Controller;
use yii\web\Response;
use yii\helpers\Url;
use Symfony\Component\Yaml\Yaml;

class SwaggerController extends Controller
{
    public $defaultAction = 'doc';
    public $host;
    public $basePath;
    public $templateFile;
    public $includePaths = [];

    public function beforeAction($action)
    {
        Yii::$app->response->off(Response::EVENT_BEFORE_SEND);
        return parent::beforeAction($action);
    }

    public function init()
    {
        parent::init();

        $this->templateFile = Yii::getAlias($this->templateFile);

        if (!is_file($this->templateFile)) {
            throw new InvalidConfigException('Invalid swagger template file: ' . $this->templateFile);
        }

        if (empty($this->includePaths)) {
            throw new InvalidConfigException('Include paths must be set');
        }
    }

    public function actions()
    {
        return [
            'doc' => [
                'class' => 'YarCode\Yii2\Swagger\SwaggerAction',
                'restUrl' => Url::to(['api'], true),
            ],
        ];
    }

    public function actionApi()
    {
        array_walk($this->includePaths, function (&$item) {
            $item = Yii::getAlias($item);
        });

        $includePaths = array_filter($this->includePaths, function ($item) {
            return is_dir($item);
        });

        $content = [];

        foreach ($includePaths as $path) {

            $newContent = $this->loadDirectory($path);

            foreach ($newContent as $key=>$value) {
                if (isset($content[$key])) {
                    $content[$key] = $content[$key] . "\n" . $newContent[$key];
                } else {
                    $content[$key] = $value;
                }
            }
        }

        /** Load config */
        $yaml = Yaml::parse(file_get_contents($this->templateFile));
        $yaml['host'] = $this->host;
        $yaml['basePath'] = $this->basePath;

        foreach ($content as $key=>$value) {
            $yaml[$key] = Yaml::parse($value);
        }

        return Yaml::dump($yaml);
    }

    /**
     * @param $path
     * @return array
     */
    protected function loadDirectory($path)
    {
        if (!is_dir($path)) {
            throw new \InvalidArgumentException("$path is not a directory");
        }

        $result = [];
        $dir = new \DirectoryIterator($path);

        foreach ($dir as $info) {
            if ($info->isDir() && !$info->isDot()) {
                $result[$info->getFilename()] = $this->mergeDirectoryFiles($info->getPathname());
            }
        }

        return $result;
    }

    /**
     * @param $path
     * @return string
     */
    protected function mergeDirectoryFiles($path)
    {
        if (!is_dir($path)) {
            throw new \InvalidArgumentException("$path is not a directory");
        }

        $dir = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
        $content = '';

        foreach ($dir as $info) {
            if ($info->isFile()) {
                $content = implode("\n", [$content, file_get_contents($info->getPathname())]);
            }
        }

        return $content;
    }
};
