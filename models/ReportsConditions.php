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
 * @property string $operation
 * @property string $function
 *
 * @property Reports $report
 */
class ReportsConditions extends \yii\db\ActiveRecord
{
    private $_config;

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
            [['attribute_name', 'function','param','plan'], 'string', 'max' => 255],

            [['attribute_name'],function(){ $this->initReportCondition(); return true;}],

            // should be called after attribute_name
            // This validator checks for param is to be required field
            [['param'],function($attribute,$param){
                    if(isset($this->currentFunction) && isset($this->currentFunction['param']) && $this->currentFunction['param'] === 'required'){
                        return isset($this->$attribute);
                    }
                    return true;
                }, 'skipOnEmpty' => false, 'message' => Yii::t('reportmanager','{attribute} is required.')],
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
                    'func' => function($attribute,$param) {
                        return $param ? "COUNT(IF([[$attribute]]=:param,1,NULL))" : 'COUNT(*)';
                    },
                    'label' => Yii::t('reportmanager','Count'),
                    'param' => 'optional',
                ],
                'max' => [
                    'func' => function($attribute,$param) {
                        return "MAX([[$attribute]])";
                    },
                    'label' => Yii::t('reportmanager','Max'),
                    'param' => NULL,
                ],
                'min' => [
                    'func' => function($attribute,$param) {
                        return "MIN([[$attribute]])";
                    },
                    'label' => Yii::t('reportmanager','Min'),
                    'param' => NULL,
                ],
                'year' => [
                    'func' => function($attribute,$param) {
                        return "Year([[$attribute]])";
                    },
                    'label' => Yii::t('reportmanager','Year'),
                    'param' => NULL,
                ],
                'month' => [
                    'func' => function($attribute,$param) {
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
        return $this->getFunctionsList($this->operation, $this->function);
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

    public function getValue()
    {
        return unserialize($this->param);
    }

    public function setValue($val)
    {
        $this->param = serialize($val);
    }

}
