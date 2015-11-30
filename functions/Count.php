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
    public function getParamType()
    {
        return 'string';
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

    /**
     * Prepare output string for rendering Table
     *
     * @return string
    public function prepareTable($attribute,$param)
    {
        return '[[attribute]]';
    }
     */

/*
    'date' => [
        'func' => function($attribute, $param) {
            return "UNIX_TIMESTAMP([[$attribute]])";
        },
        'render_tab' => function($val,$param) {
            $date = new \DateTime;
            $date->setTimestamp($val);
            $format = $param ? $param : '%Y-%m-%d';
            return $date->format($format);
        },
        'label' => Yii::t('reportmanager','Date'),
        'param' => 'optional',
        'paramType' => 'string',
    ],
*/
}
