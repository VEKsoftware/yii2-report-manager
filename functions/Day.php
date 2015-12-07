<?php

namespace reportmanager\functions;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is an abstract class containing definition of ReportsConditions Functions.
 *
 */
class Day extends Func
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
        return 'day';
    }

    /**
     * @inherit
     */
    public static function getLabel()
    {
        return Yii::t('reportmanager','Day');
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
        return 'date';
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
        if($this->driver === 'mysql')
            return "DAY([[$attribute]])";
        elseif($this->driver === 'pgsql')
            return "EXTRACT(DAY FROM [[$attribute]])";
    }

}
