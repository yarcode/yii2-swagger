<?php

namespace YarCode\Yii2\Swagger;

use yii\base\Action;

/**
 * The document display action.
 *
 * ~~~
 * public function actions()
 * {
 *     return [
 *         'doc' => [
 *             'class' => 'YarCode\Yii2\Swagger\SwaggerAction',
 *             'restUrl' => Url::to(['site/api'], true)
 *         ]
 *     ];
 * }
 * ~~~
 */
class SwaggerAction extends Action
{
    /**
     * @var string The rest url configuration.
     */
    public $restUrl;

    public function run()
    {
        $this->controller->layout = false;

        $view = $this->controller->getView();

        return $view->renderFile(__DIR__ . '/index.php', [
            'rest_url' => $this->restUrl,
        ], $this->controller);
    }
}
