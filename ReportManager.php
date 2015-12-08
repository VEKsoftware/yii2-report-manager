<?php

namespace reportmanager;

use reportmanager\models\Reports;

/**
 *
 * The main class for module ReportManager
 *
 * @property $controllerNamespace The namespace for the controller in the module ReportManager
 * @property $reporModelClass     The class which inherits reportmanager\models\Reports class
 *
 */
class ReportManager extends \yii\base\Module
{
    public $controllerNamespace = 'reportmanager\controllers';
    public $_report_classes;
    public $reportModelClass;

    /**
     * @inherit
     */
    public function init()
    {
        parent::init();
        $this->registerTranslations();
        Reports::$classes_list = $this->_report_classes;
        Reports::$module = $this;
    }

    /**
     * Setter for classes list
     *
     * @param array $rc the settings from config
     */
    public function setReportClasses($rc)
    {
        $this->_report_classes = $rc;
    }

    /**
     * Getter for classes list
     * @return array current settings from config
     */
    public function getReportClasses()
    {
        return $this->_report_classes;
    }

    /**
     * Initialization of the i18n translation module
     */
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
