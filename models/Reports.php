<?php

namespace reportmanager\models;

use Yii;
use yii\helpers\ArrayHelper;

use reportmanager\ReportManagerInterface;
use reportmanager\models\ReportsConditions;

/**
 * This is the model class for table "{{%reports}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $class_name
 * @property string $options
 * @property string $template
 *
 * @property ReportsConditions[] $reportsConditions
 */
class Reports extends \yii\db\ActiveRecord
{
    public static $classes_list;
    public $conditions;

    private $_config;
    private $_model_class;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reports}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'class_name'], 'required'],
            [['options', 'template','description'], 'string'],
            [['name'], 'string', 'max' => 128],
            [['class_name'], 'string', 'max' => 255],
            [['class_name'], 'in', 'range' => self::$classes_list, 'message' => Yii::t('reportmanager','Wrong class')],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('reportmanager', 'ID'),
            'name' => Yii::t('reportmanager', 'Report Name'),
            'description' => Yii::t('reportmanager', 'Description'),
            'class_name' => Yii::t('reportmanager', 'Data Class'),
            'options' => Yii::t('reportmanager', 'Options'),
            'template' => Yii::t('reportmanager', 'Report Template'),
        ];
    }

    protected function initClass()
    {
        if(isset($this->class_name)) {
            $this->_model_class = new $this->class_name;
            if(! $this->_model_class instanceof ReportManagerInterface)
                throw new \yii\base\InvalidParamException('The classes for ReportManager must implement interface of ReportManagerInterface class');
        }
    }

    /**
     * Preform initializtion either in init() or in afterFind
     * depending on wheather the model is new or saved.
     * Main task is readin the config of the module.
     */
    public function init()
    {
        parent::init();
        $this->initClass();
    }

    /**
     * Preform initializtion either in init() or in afterFind
     * depending on wheather the model is new or saved.
     * Main task is readin the config of the module.
     */
    public function afterFind()
    {
        parent::afterFind();
        $this->initClass();
    }

    /**
     * Returns all conditions related with the current report.
     *
     * @return \yii\db\ActiveQuery All related conditions for the Report
     */
    public function getReportsConditions()
    {
        return $this->hasMany(ReportsConditions::className(), ['report_id' => 'id'])->with('report');
    }

    /**
     * Get all available properties for the current report
     *
     * @return array list of all properties available to this report
     * Array structure is ['attribute_name'=>['attribute'=>'...', 'label' => '...', 'other' => ....],[...]]
     */
    public function getAvailableProps()
    {
        if(is_object($this->_model_class)) {
            $class = $this->_model_class->className();
            return $class::getReportManagerSettings();
        } else {
            return [];
        }
    }

    /**
     * Get report class label
     *
     * Get the label for the class used for formation of the report
     *
     * @return string Class label
     */
    public function getClassLabel()
    {
        if(is_object($this->_model_class)) {
            $class = $this->_model_class->className();
            return $class::getModelLabel();
        } else {
            return NULL;
        }
    }

}
