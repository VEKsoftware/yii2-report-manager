<?php

namespace reportmanager;

use reportmanager\models\Reports;

class ReportManager extends \yii\base\Module
{
    public $controllerNamespace = 'reportmanager\controllers';
    public $_report_classes;
    public $reportModelClass;

    public function init()
    {
        parent::init();
        $this->registerTranslations();
        Reports::$classes_list = $this->_report_classes;
    }

    public function setReportClasses($rc)
    {
        $this->_report_classes = $rc;
    }

    public function getReportClasses()
    {
        return $this->_report_classes;
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
