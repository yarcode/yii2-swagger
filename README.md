# yii2-swagger

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist yarcode/yii2-swagger
```

or add

```json
"yarcode/yii2-swagger": "*"
```

## Usage

TODO: It should be described in detail.

```php
public function init()
{
    $this->controllerMap = [
        'swagger' => [
            'class' => 'YarCode\Yii2\Swagger\SwaggerController',
            'host' => 'http://some.host',
            'basePath' => '/base/path/to/swagger/doc',,
            'templateFile' => '/@api/path/to/swagger/template.yaml',
            'includePaths' => [
                '/include/path/first',
                '/include/path/second',
            ],
        ]
    ];
    parent::init();
}
```

## License

![MIT](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)

Copyright (c) 2016 lichunqiang, YarCode
