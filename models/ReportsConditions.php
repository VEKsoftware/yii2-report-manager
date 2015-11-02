<?php

namespace reportmanager\models;

use Yii;

/**
 * This is the model class for table "reports_conditions".
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
        return 'reports_conditions';
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
            'id' => 'ID',
            'report_id' => 'Report ID',
            'attribute_name' => 'Имя поля задается через атрибут модели',
            'operation' => 'Операция, в которой участвует поле',
            'function' => 'Функция, которая применяется к полю атрибута',
        ];
    }
}
