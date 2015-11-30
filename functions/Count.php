<?php

namespace reportmanager\functions;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is an abstract class containing definition of ReportsConditions Functions.
 *
 */
class Count extends Func
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
        return 'count';
    }

    /**
     * @inherit
     */
    public function getLabel()
    {
        return Yii::t('reportmanager','Count');
    }

    /**
     * @inherit
     */
    public function getParamRequired()
    {
        return 'optional';
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
    public function prepareSql($param)
    {
        return is_array($param) && count($param)>0 ?
            "COUNT(IF([[$attribute]] IN ("
                .implode(", ",array_map(function($val){ return \Yii::$app->db->quoteValue($val); },$param))
            ."),1,NULL))"
            : 'COUNT(*)';
    }

}
