<?php

namespace reportmanager\base;

use yii\base\Behavior;
use yii\base\BootstrapInterface;
use yii\base\Application;

class Init implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $app->on(Application::EVENT_BEFORE_REQUEST, function () {
            $this->registerTranslations();
        });
    }

    public function registerTranslations()
    {
        \Yii::$app->i18n->translations['reportmanager'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'ru',
            'basePath' => '@reportmanager/messages',

            'fileMap' => [
                'reportmanager' => 'reportmanager.php',
            ],

        ];
    }

}
