<?php

namespace reportmanager\models;

use Yii;

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
    private $_enabled;

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
            [['report_id', 'attribute_name', 'operation', 'function'], 'required'],
            [['report_id'], 'integer'],
            [['operation'], 'string', 'max' => 20],
            [['attribute_name', 'function'], 'string', 'max' => 255]
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
        foreach($cond_array as $cond_params) {
            if($cond_params['id']) {
                $cond = ReportsConditions::findOne($cond_params['id']);
                if($cond->report_id !== $report_id) {
                    continue;
                }
                $conditions[] = $cond;
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

    public static function getfunctionsList($operation = NULL)
    {
        $functions = [
            'select' => [
                
            ],
        ];

        return isset($operation)? $functions[$operation] : $functions;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReport()
    {
        return $this->hasOne(Reports::className(), ['id' => 'report_id']);
    }

    public function getEnabled()
    {
        return $this->_enabled;
    }

    public function setEnabled($flag)
    {
        $this->_enabled = $flag;
    }
}
