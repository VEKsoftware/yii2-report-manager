Report Manager
==============
Extension for generation reports by end users.

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

Once the extension is installed, you need to set up it in your config file web.php (main.php) :

```php
    'modules' => [
        'reportmanager' => [
            'class' => 'reportmanager\ReportManager',
            'reportModelClass' => 'The model which must inherit an abstract class reportmanager\models\Reports',
            'reportClasses' => [
                '\app\models\ClassSearch1', // The models for tables which will be used in the report manager
                '\app\models\ClassSearch2', // The reportClasses must implement ReportManagerInterface.
                ...
                '\app\models\ClassSearchN',
            ],
        ],
    ]
```
