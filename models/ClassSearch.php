<?php

namespace reportmanager\models;

use Yii;
use yii\helpers\ArrayHelper;

use reportmanager\ReportManagerInterface;
use reportmanager\models\ReportsConditions;
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
class ClassSearch extends \yii\db\ActiveRecord
{
    public static $dynamic_labels;
    public static $table_name;
    private $_custom_attributes;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return self::$table_name;
    }

    public static function populateRecord($record, $row)
    {
        $record->_custom_attributes = array_keys($row);
        parent::populateRecord($record, $row);
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return array_merge(parent::attributes(),$this->_custom_attributes);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
//        return [
//            'id' => Yii::t('reportmanager', 'ID'),
//        ];
        return self::$dynamic_labels + parent::attributeLabels();
    }

}