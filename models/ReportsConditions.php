<?php

namespace reportmanager\models;

use Yii;

use yii\helpers\ArrayHelper;
use yii\base\ErrorException;
use reportmanager\functions\Func;

/**
 * This is the model class for table "{{%reports_conditions}}".
 *
 * @property integer $id
 * @property integer $report_id
 * @property string $attribute_name
 * @property string $col_label For SELECT operation only. The label of column for the report
 * @property string $operation
 * @property string $function
 * @property string $param
 * @property string $plan
 *
 * @property Reports $report
 */
class ReportsConditions extends \yii\db\ActiveRecord
{
    private $_config;
    private $_function;
    public $value;
    public $reportModelClass;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reports_conditions}}';
//        return 'taxi.reports_conditions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['report_id', 'attribute_name', 'operation'], 'required'],
            [['report_id'], 'integer'],
            [['operation'], 'string', 'max' => 20],
            [['attribute_name', 'function','plan'], 'string', 'max' => 255],
            [['col_label'], 'string', 'max' => 128 ],

            [['attribute_name'],function(){ $this->initReportCondition(); return true;}],

            // should be called after attribute_name
            // This validator checks for param is to be required field
            [['value'],'required', 'when' => function($model){
                    return $model->functionObj->paramRequired === 'required';
                },
            ],

            [['value'], 'validateValues'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('reportmanager', 'ID'),
            'report_id' => Yii::t('reportmanager', 'Report'),
            'attribute_name' => Yii::t('reportmanager', 'Attribute Name'),
            'col_label' => Yii::t('reportmanager', 'Column Label'),
            'operation' => Yii::t('reportmanager', 'Operation'),
            'function' => Yii::t('reportmanager', 'Function on Attr.'),
            'order' => Yii::t('reportmanager', 'Index'),
        ];
    }

    public static function createConditions($cond_array,$report_id)
    {
        $conditions = [];
        $ids = array_filter(ArrayHelper::getColumn($cond_array,'id'));
        $cond = ArrayHelper::index(ReportsConditions::find()->where(['and', ['id' => $ids], ['report_id' => $report_id]])->with('report')->all(),'id');
        foreach($cond_array as $k => $cond_params) {
            if($cond_params['id'] && isset($cond[$cond_params['id']])) {
                $cond_item = $cond[$cond_params['id']];
                $cond_item->order = $k;
                $conditions[] = $cond_item;
            } else {
                $conditions[] = new self(['report_id' => $report_id, 'order' => $k]);
            }
        }
        return $conditions;
    }

    public static function getOperationsList()
    {
        return [
            'select' => Yii::t('reportmanager','Column'),
            'where' => Yii::t('reportmanager','Condition'),
            'group' => Yii::t('reportmanager','Grouping'),
            'order' => Yii::t('reportmanager','Order'),
        ];
    }

    public function getConditionLabel()
    {
        return $this->config['label'];
    }

    public static function getFunctionsList($operation = NULL, $function = NULL)
    {
        $functions = Func::listFunctions();

        if(isset($function)) {
            return isset($functions[$function]) ? $functions[$function] : NULL;
        }
        return $functions;

    }

    public function getFunctionObj()
    {
        return $this->_function;
    }

    public function setFunctionObj($func_id)
    {
        $this->function = $func_id;
        $this->_function = Func::instantiate(['condition' => $this]);
    }

    public function init()
    {
        parent::init();
        if(!isset($this->operation)) $this->operation = 'select';
        $this->functionObj = $this->function;
        $this->order = 0;
    }

    public function initReportCondition()
    {
        if(!$this->report) return;
        $all_conditions = ArrayHelper::index($this->report->getAvailableProps(),'attribute');
        $this->_config = isset($this->attribute_name) && isset($all_conditions[$this->attribute_name]) ? $all_conditions[$this->attribute_name] : NULL;
        $this->functionObj = $this->function;
        return true;
    }

    public function afterFind()
    {
        parent::afterFind();
        if($this->param) $this->value = unserialize($this->param);
        $this->initReportCondition();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReport()
    {
//        return $this->hasOne(Yii::$app->controller->module->reportModelClass, ['id' => 'report_id']);
        return $this->hasOne(Reports::className(), ['id' => 'report_id']);
        if(! $this->reportModelClass) {
            throw new ErrorException('ReportsConditions::reportModelClass property must by initialized by the child of abstract model \reportmanager\Reports');
        }
        return $this->hasOne($this->reportModelClass, ['id' => 'report_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConfig()
    {
        return $this->_config;
    }

    public function validateValues($attribute, $param)
    {
        $value = $this->$attribute;

        // If paramType is set and multiple we expect an array
        if($this->functionObj->isMultiple) {
            if(!is_array($value)) {
                $this->addError($attribute, Yii::t('Parameter must be an array. Check for option "multiple" in config of ReportManager'));
                return false;
            }

            foreach ($value as $k => $v) {
                if (! $v) {
                    unset($value[$k]);
                    continue;
                }
                if(! $this->validateValue($attribute, $v)) return false;
            }
            return true;
        } else {
            // Value is not an array
            return $this->validateValue($attribute, $value);
        }
    }

    public function validateValue($attribute, $val)
    {
        if ($this->functionObj->paramType === NULL) return false;

        switch($this->functionObj->paramType) {
        case 'integer':
            if (! is_int($val)) {
                $this->addError($attribute, Yii::t('reportmanager', '{attribute} must be integer'));
                return false;
            }
            break;
        case 'numeric':
            if (! is_numeric($val)) {
                $this->addError($attribute, Yii::t('reportmanager', '{attribute} must be numeric'));
                return false;
            }
            break;
        case 'string':
            if (! is_string($val)) {
                $this->addError($attribute, Yii::t('reportmanager', '{attribute} must be string'));
                return false;
            }
            break;
        case 'in':
            if (! isset($this->config['values']) || ! is_array($this->config['values']) || ! array_key_exists($val, $this->config['values'])) {
                $this->addError($attribute, Yii::t('reportmanager', '{attribute} is not within declared values'));
                return false;
            }
            break;
        case 'date':
            if (isset(Yii::$app->params['dateFormat']) && ! \DateTime::createFromformat(Yii::$app->params['dateFormat'],$val)) {
                $this->addError($attribute, Yii::t('reportmanager', '{attribute} must be date of format {format}',['format' => Yii::$app->params['dateFormat']]));
                return false;
            } elseif (! new \DateTime($val)) {
                $this->addError($attribute, Yii::t('reportmanager', '{attribute} must be date'));
                return false;
            }
            break;
        case 'date range':
            $dates = split(' - ',$val);
            if (count($dates) !== 2) {
                $this->addError($attribute, Yii::t('reportmanager', '{attribute} must contain two date separated by " - "'));
                return false;
            } elseif (isset(Yii::$app->params['dateFormat']) && ! (
                \DateTime::createFromformat(Yii::$app->params['dateFormat'],$dates[0]) &&
                \DateTime::createFromformat(Yii::$app->params['dateFormat'],$dates[1]) )
            ) {
                $this->addError($attribute, Yii::t('reportmanager', '{attribute} must be date of format {format}',['format' => Yii::$app->params['dateFormat']]));
                return false;
            } elseif (! new \DateTime($dates[0]) || ! new \DateTime($dates[1])) {
                $this->addError($attribute, Yii::t('reportmanager', '{attribute} must be date'));
                return false;
            }
            break;
        }
        return true;
    }


    /**
     *
     * return the column alias to use in SQL
     *
     * @return string
     */
    public function getAlias()
    {
        if($this->operation === 'select') {
            return 'da_'.$this->id;
        } else {
            return NULL;
        }
    }

    /**
     *
     * return the column label
     *
     * @return string
     */
    public function getLabel()
    {
        if($this->operation === 'select') {
            return $this->col_label;
        } else {
            return NULL;
        }
    }

    /**
     *
     * Apply current condition will be applied to a query using this function.
     *
     * @param \yii\db\ActiveQuery $query The query object which current condition must be applied to
     * @param integer $index The index of this condition. It will be used for alias formation
//     * @param \reportmanager\ReportManagerInterface $model An object of the class related to the report. We use it to add a property from SELECT statement.
     */
    public function prepareQuery($query, $index)
    {
        $field = $this->functionObj->prepareSql();
        switch($this->operation) {
            case 'select':
                // Here I need to add property to the target class accroding to alias
                $query->addSelect([$this->alias => $field]);
                break;
            case 'where':
                $query->andWhere($field);
                break;
            case 'group':
                $query->addGroupBy($field);
                break;
            case 'order':
                $query->addOrderBy($field);
                break;
        }
    }

    public function beforeSave($insert)
    {
        // We need to serialize value into param attribute
        $this->param = serialize($this->value);
        return parent::beforeSave($insert);
    }
}
