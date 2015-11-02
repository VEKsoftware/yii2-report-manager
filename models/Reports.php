<?php

namespace reportmanager\models;

use Yii;

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
            [['name', 'class_name', 'options', 'template'], 'required'],
            [['options', 'template'], 'string'],
            [['name'], 'string', 'max' => 128],
            [['class_name'], 'string', 'max' => 255]
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
            'class_name' => Yii::t('reportmanager', 'Data Class'),
            'options' => Yii::t('reportmanager', 'Options'),
            'template' => Yii::t('reportmanager', 'Report Template'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportsConditions()
    {
        return $this->hasMany(ReportsConditions::className(), ['report_id' => 'id']);
    }
}
