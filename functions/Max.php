<?php

namespace reportmanager\functions;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is an abstract class containing definition of ReportsConditions Functions.
 *
 */
class Max extends Func
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
        return 'max';
    }

    /**
     * @inherit
     */
    public function getLabel()
    {
        return Yii::t('reportmanager','Max');
    }

    /**
     * @inherit
     */
    public function getParamRequired()
    {
        return NULL;
    }

    /**
     * @inherit
     */
    public function getType()
    {
        return 'string';
    }

    /**
     * @inherit
     */
    public function getParamType()
    {
        return NULL;
    }

    /**
     * @inherit
     */
    public function getIsMultiple()
    {
        return false;
    }

    /**
     * Prepare SQL string for ActiveQuery
     *
     * @return string
     */
    public function prepareSql()
    {
        $attribute = $this->condition->attribute_name;
        $param = $this->condition->value;
        return is_array($param) && count($param)>0 ?
            "MAX(IF([[$attribute]] IN ("
                .implode(", ",array_map(function($val){ return \Yii::$app->db->quoteValue($val); },$param))
            ."),1,NULL))"
            : "MAX([[$attribute]])";

}
