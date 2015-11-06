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
            [['operation'], 'string'],
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
