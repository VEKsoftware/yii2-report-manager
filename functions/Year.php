<?php

namespace reportmanager\functions;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is an abstract class containing definition of ReportsConditions Functions.
 *
 */
class Year extends Func
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
        return 'year';
    }

    /**
     * @inherit
     */
    public static function getLabel()
    {
        return Yii::t('reportmanager','Year');
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
     * @inherit
     */
    public function prepareSql()
    {
        $attribute = $this->condition->attribute_name;
        $param = $this->condition->value;
        if($this->driver === 'mysql')
            return "YEAR([[$attribute]])";
        elseif($this->driver === 'pgsql')
            return "EXTRACT(YEAR FROM [[$attribute]])";
    }
}
