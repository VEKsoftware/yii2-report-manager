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
abstract class Reports extends \yii\db\ActiveRecord
{
    public static $classes_list;
    public $conditions;
    public static $module;
    public $show;
    public $graph_x;

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
            [['name', 'class_name','group_id','show'], 'required'],
            [['options', 'template','description'], 'string'],
            [['name'], 'string', 'max' => 128],
            [['show'], 'string', 'max' => 12],
            [['show'],'in','range' => ['table','graph','both']],
            [['graph_x'],'in','range' => ArrayHelper::getColumn($this->columns,'alias')],
            [['group_id'],'integer'],
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
            'creator.name' => Yii::t('reportmanager','Creator'),
            'group.name' => Yii::t('reportmanager','Group'),
            'show' => Yii::t('reportmanager','Display as'),
            'graph_x' => Yii::t('reportmanager','Field for X axis'),
        ];
    }

    public static function listShowOptions()
    {
        return [
            'table' => Yii::t('reportmanager','Table'),
            'graph' => Yii::t('reportmanager','Graph'),
            'both'  => Yii::t('reportmanager','Both'),
        ];
    }

    public function transactions()
    {
        return [
            'default' => self::OP_DELETE,
        ];
    }

    /**
     * Get creator of the report
     *
     * @return yii\db\ActiveQuery
     */
    public abstract function getCreator();

    /**
     * Get user group having access to this report
     *
     * @return yii\db\ActiveQuery
     */
    public abstract function getGroup();

    /**
     * Check wheather the access to the report is allowed to current user
     *
     * @return boolean
     */
    public abstract function isAllowed($operation);

    /**
     * Get list of all available groups
     *
     * @return array ['value' => 'label']
     */
    public static abstract function getGroupList();

    protected function initClass()
    {
        if(isset($this->class_name)) {
            $this->_model_class = new $this->class_name;
            if(! $this->_model_class instanceof ReportManagerInterface)
                throw new \yii\base\InvalidParamException('The classes for ReportManager must implement interface of ReportManagerInterface class');
        }
    }

    /**
     *
     * Instantiate the model class.
     *
     * The children of \reportmanager\models\Reports may overload this class
     * to use multiple childer. Otherwise the class from reportModelClass parameter will be use for creation
     * of the object,
     *
     * @return \reportmanager\models\Reports
     */
    public static function instantiate($row)
    {
        if (get_called_class() !== 'reportmanager\models\Reports') {
            return new static;
        }

        $class = static::$module->reportModelClass;

        if (! $class) {
            throw new \yii\base\ErrorException('You need to specify reportModelClass variable in model ReportClass configuration.');
        }

        $rc = new \ReflectionClass($class);
        if (! $rc->isSubClassOf('\reportmanager\models\Reports')) {
            throw new \yii\base\ErrorException('The reportModelClass must be a child of \reportmanager\models\Reports.');
        }

        return $class::instantiate($row);
    }

    /**
     * This method is used to list all reports in the index page.
     * You can overload this method in order to restrict access for the users to some reports.
     *
     * @return \yii\db\ActiveQuery
     */
    public static function findReports()
    {
        if (get_called_class() !== 'reportmanager\models\Reports') {
            return static::find();
        }

        $class = static::$module->reportModelClass;

        if (! $class) {
            throw new \yii\base\ErrorException('You need to specify reportModelClass variable in model ReportClass configuration.');
        }

        $rc = new \ReflectionClass($class);
        if (! $rc->isSubClassOf('\reportmanager\models\Reports')) {
            throw new \yii\base\ErrorException('The reportModelClass must be a child of \reportmanager\models\Reports.');
        }

        return $class::findReports();
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
        $options = unserialize($this->options);
        if(isset($options['show'])) $this->show = $options['show'];
        if(isset($options['graph_x'])) $this->graph_x = $options['graph_x'];
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
//            ->orderBy(['operation' => SORT_ASC,'attribute_name' => SORT_ASC,'function' => SORT_ASC])
            ->orderBy(['order' => SORT_ASC])
            ->inverseOf('report');
    }

    /**
     * change order of conditons so that $condition_id wolud go into $direction
     *
     * @param $conditon_id integer The id of ReportsConditions element
     * @param $direction string Either "up" or "down"
     *
     * @return \yii\db\ActiveQuery All related conditions for the Report
     */
    public function moveCondition($condition_id, $direction)
    {
        $conditions = $this->reportsConditions;
        if(count($conditions) <= 1) return;

        if($direction === 'up') {
            foreach($conditions as $k => $v) {
                if($v->id === $condition_id && $k > 0) {
                    $v->order = $k-1;
//                    $tmp = $v;
//                    $v = $conditions[$k-1];
//                    $v->order = $k;
//                    $conditions[$k-1] = $tmp;
                    $conditions[$k-1]->order = $k;
                }
            }
        } elseif($direction === 'down') {
            $max_key = count($conditions) - 1;
            foreach($conditions as $k => $v) {
                if($v->id === $condition_id && $k < $max_key) {
                    $v->order = $k+1;
//                    $tmp = $v;
//                    $v = $conditions[$k+1];
//                    $v->order = $k;
//                    $conditions[$k+1] = $tmp;
                    $conditions[$k+1]->order = $k;
                }
            }
        }
//        $this->reportsConditions = $conditions;
    }

    /**
     * Save the changes in all all related conditions
     *
     * @return \yii\db\ActiveQuery All related conditions for the Report
     */
    public function saveConditions()
    {
        foreach($this->reportsConditions as $k => $v) {
            $v->save();
        }
    }

    public function getColumns()
    {
        return array_filter($this->reportsConditions,function($val){ return $val->alias !== NULL; });
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

    public function generateReport($params = NULL)
    {
        if(!is_object($this->_model_class)) {
            throw new \yii\base\InvalidParamException('Report::$_model_class must be an object');
        }

        if(! $this->_model_class instanceof ReportManagerInterface) {
            throw new \yii\base\InvalidParamException('Class '.$this->_model_class->className().' for ReportManager must implement interface of ReportManagerInterface class');
        }

        $dataProvider = $this->_model_class->search($params);
        if(! $dataProvider instanceof ActiveDataProvider) {
            throw new \yii\base\InvalidParamException('The ReportManagerInterface::search() method must return ActiveDataProvider object');
        }

        $query = $dataProvider->query;

        // Remove all select statements from initial query to prepare for the next cycle
        $query->select([]);

        $columns = [];
        foreach($this->reportsConditions as $index => $cond) {
            // !!!!! May be dataProvider shoud be sent instead of query ?????
            $cond->prepareQuery($query,$index);
        }

        $query_class = $query->modelClass;
        ClassSearch::$table_name = $query_class::tableName();
        ClassSearch::$dynamic_labels = $this->columns;
        ClassSearch::$report = $this;

        $sql = $query->createCommand()->rawSql;
        $dataProvider->query=ClassSearch::findBySql($sql);

        return $dataProvider;
    }

    public function beforeSave($insert)
    {
        $this->creator_id = Yii::$app->user->id;
        $options = unserialize($this->options);
        $options['show'] = $this->show;
        $options['graph_x'] = $this->graph_x;
        $this->options = serialize($options);
        return true;
    }

    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            return ReportsConditions::deleteAll(['report_id' => $this->id]);
        } else {
            return false;
        }
    }

}
