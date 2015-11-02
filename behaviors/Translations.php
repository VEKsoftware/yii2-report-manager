<?php

namespace reportmanager;

use yii\base\Behavior;
use yii\base\Controller;
use yii\db\ActiveRecord;

class Translations extends Behavior
{
    public function events()
    {
        return [
            ActiveRecord::EVENT_INIT => 'registerTranslations',
            Controller::EVENT_BEFORE_ACTION => 'registerTranslations',
        ];
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
