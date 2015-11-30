<?php

namespace reportmanager\functions;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is an abstract class containing definition of ReportsConditions Functions.
 *
 */
class None extends Func
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
        return 'none';
    }

    /**
     * @inherit
     */
    public function getLabel()
    {
        return Yii::t('reportmanager','None');
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
    public function getParamType()
    {
        return NULL;
    }

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
