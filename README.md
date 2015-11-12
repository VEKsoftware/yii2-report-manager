Report Manager
==============
Extension for generation reports and setting plans

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist veksoftware/yii2-report-manager "*"
```

or add

```
"veksoftware/yii2-report-manager": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, you need to set it up in your config file web.php (main.php) :

```php
    'modules' => [
        'reportmanager' => [
            'class' => 'reportmanager\ReportManager',
            'reportClasses' => [
                '\app\models\ClassSearch1',
                '\app\models\ClassSearch2',
                ...
                '\app\models\ClassSearchN',
            ],
        ]
    ]
```

The reportClasses must implement ReportManagerInterface.
