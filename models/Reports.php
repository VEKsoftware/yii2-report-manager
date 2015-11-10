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
    public $conditions;

    private $_config;

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

    /**
     * Preform initializtion either in init() or in afterFind
     * depending on wheather the model is new or saved.
     * Main task is readin the config of the module.
     */
    public function init()
    {
        parent::init();
        $this->retrieveConfig();
    }

    /**
     * Preform initializtion either in init() or in afterFind
     * depending on wheather the model is new or saved.
     * Main task is readin the config of the module.
     */
    public function afterFind()
    {
        parent::afterFind();
        $this->retrieveConfig();
    }

    public function validateCondition($attribute, $params)
    {
        if(!is_array($this->$attribute)) {
            $this->addError($attribute,Yii::t('reportmanager','{attribute} must be an array'));
            return false;
        }

        return ReportsConditions::loadMultiple($this->conditions,$this->$attribute,'');
    }

    public function loadConditions($cond_array)
    {
        foreach($cond_array as $cond_params) {
            if(isset($cond_params['id'])) {
                $cond = ReportsConditions::findOne($cond_params['id']);
                if($cond->report_id !== $this->id) {
//                    $this->
                }
                $this->conditions[] = $cond;
            } else {
                $this->conditions[] = new ReportsConditions(['report_id' => $this->id]);
            }
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportsConditions()
    {
        return $this->hasMany(ReportsConditions::className(), ['report_id' => 'id']);
    }

    /**
     * Get all available properties for the current report
     *
     * @return array list of all properties available to this report
     * Array structure is ['attribute_name'=>['attribute'=>'...', 'label' => '...', 'other' => ....],[...]]
     */
    public function getAvailableProps()
    {
        return isset($this->_config['properties'])? ArrayHelper::index($this->_config['properties'],'attribute'): NULL;
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
