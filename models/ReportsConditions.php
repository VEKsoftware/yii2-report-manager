<?php

namespace reportmanager\models;

use Yii;
//use reportmanager\behaviors\Translations;

/**
 * This is the model class for table "{{%reports_conditions}}".
 *
 * @property integer $id
 * @property integer $report_id
 * @property string $attribute_name
 * @property string $operation
 * @property string $function
 */
class ReportsConditions extends \yii\db\ActiveRecord
{
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
            'report_id' => Yii::t('reportmanager', 'Report ID'),
            'attribute_name' => Yii::t('reportmanager', 'Имя поля задается через атрибут модели'),
            'operation' => Yii::t('reportmanager', 'Операция, в которой участвует поле'),
            'function' => Yii::t('reportmanager', 'Функция, которая применяется к полю атрибута'),
        ];
    }

/*
    public function behaviors()
    {
        return [
            'translations' => [
                'class' => Translations::className(),
            ],
        ];
    }
*/
}
