<?php

namespace reportmanager;

class ReportManager extends \yii\base\Module
{
    public $controllerNamespace = 'reportmanager\controllers';
    public $content;

    public function init()
    {
        parent::init();
        $this->registerTranslations();
    }

    public function registerTranslations()
    {
        \Yii::$app->i18n->translations['reportmanager'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en',
            'basePath' => '@reportmanager/messages',

            'fileMap' => [
                'reportmanager' => 'reportmanager.php',
            ],

        ];
    }

}
