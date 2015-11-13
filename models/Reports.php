<?php

namespace reportmanager\models;

use Yii;
use yii\helpers\ArrayHelper;

use reportmanager\ReportManagerInterface;
use reportmanager\models\ReportsConditions;
use reportmanager\models\ClassSearch;
use yii\data\ActiveDataProvider;

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
        return $this->hasMany(ReportsConditions::className(), ['report_id' => 'id'])
            ->with('report')
            ->orderBy(['operation' => SORT_ASC,'attribute_name' => SORT_ASC,'function' => SORT_ASC]);
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

    public function generateReport()
    {
        if(!is_object($this->_model_class)) {
            throw new \yii\base\InvalidParamException('Report::$_model_class must be an object');
        }

        if(! $this->_model_class instanceof ReportManagerInterface) {
            throw new \yii\base\InvalidParamException('Class '.$this->_model_class->className().' for ReportManager must implement interface of ReportManagerInterface class');
        }

        $dataProvider = $this->_model_class->search(NULL);
        if(! $dataProvider instanceof ActiveDataProvider) {
            throw new \yii\base\InvalidParamException('The ReportManagerInterface::search() method must return ActiveDataProvider object');
        }

        $query = $dataProvider->query;


        // Remove all select statements from initial query to prepare for the next cycle
//        $query->select($query_class::primaryKey());
        $query_class = $query->modelClass;
//        $query->select($query_class::tableName().'.*');
        $query->select([]);

        foreach($this->reportsConditions as $index => $cond) {
            // !!!!! May be dataProvider shoud be sent instead of query ?????
            $cond->prepareQuery($query,$index);
        }

        ClassSearch::$table_name = $query_class::tableName();
        ClassSearch::$dynamic_attributes = array_keys($query->select);
//        var_dump(ClassSearch::$dynamic_attributes);die();
//        ClassSearch::$classPrimaryKey = $query_class::primaryKey();
        $sql = $query->createCommand()->rawSql;
        $dataProvider->query=ClassSearch::findBySql($sql);
//        var_dump($query_class::primaryKey()); die();
        return $dataProvider;
    }
}
