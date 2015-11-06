<?php

namespace reportmanager\models;

use Yii;
use yii\helpers\ArrayHelper;

use reportmanager\models\ReportsConditions;

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
    public static $classes_list;
    private $_config;
    public $test;

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
            [['name', 'class_name'], 'required'],
            [['options', 'template','description'], 'string'],
            [['name'], 'string', 'max' => 128],
            [['class_name'], 'string', 'max' => 255],
            [['test'],'string'],
            [['class_name'], 'in', 'range' => ArrayHelper::getColumn(self::$classes_list,'class'), 'message' => Yii::t('reportmanager','Wrong class')],
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
            'description' => Yii::t('reportmanager', 'Description'),
            'class_name' => Yii::t('reportmanager', 'Data Class'),
            'options' => Yii::t('reportmanager', 'Options'),
            'template' => Yii::t('reportmanager', 'Report Template'),
        ];
    }

    protected function retrieveConfig()
    {
        if(isset($this->class_name)) {
            $list = ArrayHelper::index(self::$classes_list,'class');
            if(isset($list[$this->class_name])) {
                $this->_config = $list[$this->class_name];
            }
        }
    }

    public function init()
    {
        parent::init();
        $this->retrieveConfig();
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->retrieveConfig();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportsConditions()
    {
        return $this->hasMany(ReportsConditions::className(), ['report_id' => 'id']);
    }

    /**
     * Get all properties for the current report
     *
     * @return \yii\db\ActiveQuery list of all properties available to this peport
     */
    public function getAvailableProps()
    {
        return isset($this->_config['properties'])? $this->_config['properties']: NULL;
    }

    /**
     * Get report class label
     *
     * Get the label for the class used for formation of the report
     *
     * @return string Class label
     */
    public function getClassLabel()
    {
        return isset($this->_config['label'])? $this->_config['label']:'';
    }

}
