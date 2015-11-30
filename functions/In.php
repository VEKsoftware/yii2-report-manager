<?php

namespace reportmanager\functions;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is an abstract class containing definition of ReportsConditions Functions.
 *
 */
class In extends Func
{
    /**
     * The parent ReportsConditions object
     */
    public $condition;

    /**
     * @inherit
     */
    public function getId()
    {
        return 'in';
    }

    /**
     * @inherit
     */
    public static function getLabel()
    {
        return Yii::t('reportmanager','Among Values');
    }

    /**
     * @inherit
     */
    public function getParamRequired()
    {
        return 'required';
    }

    /**
     * @inherit
     */
    public function getType()
    {
        return 'integer';
    }

    /**
     * @inherit
     */
    public function getParamType()
    {
        return 'string';
    }

    /**
     * @inherit
     */
    public function getIsMultiple()
    {
        return true;
    }

    /**
     * Prepare SQL string for ActiveQuery
     *
     * @return string
     */
    public function prepareSql()
    {
        $param = $this->condition->value;
        $attribute = $this->condition->attribute_name;
        return "[[$attribute]] IN ("
            .implode(", ",array_map(function($val){ return \Yii::$app->db->quoteValue($val); },$param))
        .")";
    }

}
