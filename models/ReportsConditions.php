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
            [['operation'], 'in', 'range' => array_keys($this->operationsList)],
            [['attribute_name', 'function','plan'], 'string', 'max' => 255],
            [['col_label'], 'string', 'max' => 128 ],

            [['attribute_name'],function(){ $this->initReportCondition(); return true;}],

            // should be called after attribute_name
            [['value'], 'validateValues'],

            // This validator checks for param is to be required field
            [['value'],'required', 'when' => function($model){
                    return $model->functionObj->paramRequired === 'required';
                },
                'whenClient' => new \yii\web\JsExpression('function(attribute,value){return false;}'),
            ],


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
//*
        $known_cond = ArrayHelper::index(ReportsConditions::find()
                ->where(['and', ['id' => $ids], ['report_id' => $report_id]])
                ->orderBy('order')
                ->with('report')
                ->all()
        ,'id');
//*/
//        $known_cond = 
        foreach($cond_array as $k => $cond_params) {
            if($cond_params['id'] && isset($known_cond[$cond_params['id']])) {
                $cond_item = $cond[$cond_params['id']];
                $cond_item->order = $k+1;
                $conditions[] = $cond_item;
            } else {
                $conditions[] = new self(['report_id' => $report_id, 'order' => $k+1]);
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
            'order_inv' => Yii::t('reportmanager','Order Inversed'),
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
        return $this->hasOne(Reports::className(), ['id' => 'report_id']);
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
                $this->addError($attribute, Yii::t('reportmanager','Parameter must be an array. Check for option "multiple" in config of ReportManager'));
                return false;
            }

            foreach ($value as $k => $v) {
                if (! $v) {
                    unset($this->value[$k]);
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
     */
    public function prepareQuery($query)
    {
        $field = $this->functionObj->prepareSql();
        if(! $field) return NULL;
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
            case 'order_inv':
                $query->addOrderBy([$field => SORT_DESC]);
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
