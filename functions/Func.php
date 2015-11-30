<?php

namespace reportmanager\functions;

use Yii;
use yii\helpers\ArrayHelper;

use reportmanager\models\ReportsConditions;

/**
 * This is an abstract class containing definition of ReportsConditions Functions.
 *
 */
abstract class Func extends \yii\base\Object
{
    /**
     * The parent ReportsConditions object
     */
    public $condition;

    /**
     * The list of all functions in the system
     *
     * @return array [function_id => function_class]
     */
    public static function listFunctions(){
        return [
            'count' => Count::className(),
            'date' => Date::className(),
        ];
    }

    /**
     * Instantiates proper function class for current ReportsConditions object
     *
     * @return \reportmanager\functions\Function
     */
    public static function instantiate($row){
        if(is_a($row['condition'],'reportmanager\models\ReportsConditions')) {
            $condition = $row['condition'];
            $funcs_list = static::listFunctions();
            if(isset($funcs_list[$condition->function])) {
                return new $funcs_list[$condition->function]($row);
            } else {
                return new None($row);
//                throw new \yii\base\ErrorException('Unknown function id "'.$condition->function.'".');
            }
        } else {
            throw new \yii\base\ErrorException('You need to provide condition property for instantiation of the Function object.');
        }
    }

    /**
     * The id string for current function
     *
     * For example, 'sin'
     *
     * @return string
     */
    public abstract function getId();

    /**
     * The label string for current function
     *
     * For example, 'Sine'
     *
     * @return string
     */
    public abstract function getLabel();

    /**
     * Must return the param option
     *
     * @return string One of 'required', 'optional' or ''/NULL
     */
    public abstract function getParamRequired();

    /**
     * Type of output of the function
     *
     * @return string One of 'string', 'date', 'integer'
     */
    public abstract function getType();

    /**
     * Type of parameter accepted by the function
     *
     * @return string One of 'string', 'date', 'date range'
     */
    public abstract function getParamType();

    /**
     * Is the parameter to this function is multiple?
     *
     * @return string One of 'string', 'date', 'integer'
     */
    public abstract function getIsMultiple();

    /**
     * Prepare SQL string for ActiveQuery
     *
     * @return string
     */
    public function prepareSql($param)
    {
        return '[[attribute]]';
    }

    /**
     * Prepare output string for rendering Table.
     *
     * The outputh of the method will be merged into GridView::columns property and must be in its format.
     *
     * @return array|string
     */
    public function prepareTable()
    {
        return $this->condition->alias;
    }

    /**
     * Prepare output string for rendering Graph
     *
     * @return string
     */
    public function prepareGraph($val)
    {
        return $val;
    }

}
