<?php

namespace reportmanager\models;

use Yii;

use yii\helpers\ArrayHelper;

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
    public $value;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reports_conditions}}';
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
                    return isset($model->currentFunction)
                        && isset($model->currentFunction['param'])
                        && $model->currentFunction['param'] === 'required'
                    ;
                    return true;
                },
            ],

            [['value'], 'validateValues'],

/*
            [['value'],
                'each',
                'rule' => [$this->config['type']],
                'when' => function($model,$attribute){
                    return isset($model->currentFunction)
                        && isset($model->currentFunction['paramType'])
                        && $model->currentFunction['paramType'] === 'multiple'
                    ;
                },
            ],
            [['value'], 'each', 'rule' => [$this->config['type']], 'when' => function($model,$attribute){
                    return isset($model->currentFunction)
                        && isset($model->currentFunction['paramType'])
                        && $model->currentFunction['paramType'] === 'multiple'
                    ;
                },
            ],
            [['value'], 'string', 'max' => 128, 'when' => function($model,$attribute){
                    return isset($model->currentFunction)
                        && isset($model->currentFunction['paramType'])
                        && $model->currentFunction['paramType'] === 'string'
                    ;
                },
            ],
*/

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
        ];
    }

    public static function createConditions($cond_array,$report_id)
    {
        $conditions = [];
        $ids = array_filter(ArrayHelper::getColumn($cond_array,'id'));
        $cond = ArrayHelper::index(ReportsConditions::find()->where(['and', ['id' => $ids], ['report_id' => $report_id]])->with('report')->all(),'id');
        foreach($cond_array as $cond_params) {
            if($cond_params['id'] && isset($cond[$cond_params['id']])) {
                $conditions[] = $cond[$cond_params['id']];
            } else {
                $conditions[] = new self(['report_id' => $report_id]);
            }
        }
        return $conditions;
    }

    public static function getOperationsList()
    {
        return [
            'select' => Yii::t('reportmanager','Select'),
            'where' => Yii::t('reportmanager','Condition'),
            'group' => Yii::t('reportmanager','Grouping'),
            'order' => Yii::t('reportmanager','Order'),
        ];
    }

    public static function getFunctionsList($operation = NULL, $function = NULL)
    {
        $functions = [
            'select' => [
                'count' => [
                    'func' => function($attribute, $param) {
                        return is_array($param) && count($param)>0 ? "COUNT(IF([[$attribute]] IN ('".implode("','",ArrayHelper::htmlEncode($param))."'),1,NULL))" : 'COUNT(*)';
                    },
                    'label' => Yii::t('reportmanager','Count'),
                    'param' => 'optional',
                    'paramType' => 'multiple',
                ],
                'max' => [
                    'func' => function($attribute, $param) {
                        return "MAX([[$attribute]])";
                    },
                    'label' => Yii::t('reportmanager','Max'),
                    'param' => NULL,
                ],
                'min' => [
                    'func' => function($attribute, $param) {
                        return "MIN([[$attribute]])";
                    },
                    'label' => Yii::t('reportmanager','Min'),
                    'param' => NULL,
                ],
                'year' => [
                    'func' => function($attribute, $param) {
                        return "Year([[$attribute]])";
                    },
                    'label' => Yii::t('reportmanager','Year'),
                    'param' => NULL,
                ],
                'month' => [
                    'func' => function($attribute, $param) {
                        return "Month([[$attribute]])";
                    },
                    'label' => Yii::t('reportmanager','Month'),
                    'param' => NULL,
                ],
            ],
            'where' => [
            ],
            'group' => [
            ],
            'order' => [
            ],
        ];

        return isset($operation) && isset($functions[$operation]) ? (
            isset($function) && isset($functions[$operation][$function]) ? $functions[$operation][$function] : $functions[$operation]
        ) : $functions;
    }

    public function getCurrentFunction()
    {
        if(!isset($this->operation) || !isset($this->function)) return NULL;
        $func_list = self::getFunctionsList($this->operation);
        if(!is_array($func_list)) return NULL;
        return isset($func_list[$this->function]) ? $func_list[$this->function] : NULL;
    }

    public function initReportCondition()
    {
        if(!$this->report) return;
        $all_conditions = ArrayHelper::index($this->report->getAvailableProps(),'attribute');
        $this->_config = isset($this->attribute_name) && isset($all_conditions[$this->attribute_name]) ? $all_conditions[$this->attribute_name] : NULL;
    }

    public function init()
    {
        parent::init();
//        $this->initReportCondition();
        if(!isset($this->operation)) $this->operation = 'select';
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->initReportCondition();
        if($this->param) $this->value = unserialize($this->param);
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

/*
    public function getValue()
    {
        return unserialize($this->param);
    }

    public function setValue($val)
    {
        $this->param = serialize($val);
    }
*/
    public function validateValues($attribute, $param)
    {
        $value = $this->$attribute;

        // If paramType is set and multiple we expect an array
        if(isset($this->currentFunction)
                && isset($this->currentFunction['paramType'])
                && $this->currentFunction['paramType'] === 'multiple'
        ) {
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
        switch($this->config['type']) {
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
            if (! is_int($val)) {
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
            } elseif (! \DateTime($val)) {
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
            } elseif (! \DateTime($dates[0]) || ! \DateTime($dates[1])) {
                $this->addError($attribute, Yii::t('reportmanager', '{attribute} must be date'));
                return false;
            }
            break;
        }
        return true;
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
        $field = $this->currentFunction && $this->currentFunction['func'] ?
                call_user_func($this->currentFunction['func'],$this->attribute_name, $this->value) : $this->attribute_name;
        switch($this->operation) {
            case 'select':
                // Here I need to add property to the target class accroding to alias
                //$alias = "dynamic_attributes_$index";
                $alias = "da_$index";
                $query->addSelect([$alias => $field]);
                return [$alias => $this->col_label];
            case 'where':
                $query->andWhere($field);
                return [];
            case 'group':
                $query->addGroupBy($field);
                return [];
            case 'order':
                $query->addOrderBy($field);
                return [];
        }
    }

    public function beforeSave($insert)
    {
        // We need to serialize value into param attribute
        $this->param = serialize($this->value);
        return parent::beforeSave($insert);
    }
}
