<?php

namespace reportmanager\functions;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is an abstract class containing definition of ReportsConditions Functions.
 *
 */
class NotNull extends Func
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
        return 'not null';
    }

    /**
     * @inherit
     */
    public static function getLabel()
    {
        return Yii::t('reportmanager','Not Null');
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
     * @inherit
     */
    public function prepareSql()
    {
        $attribute = $this->condition->attribute_name;
        $param = $this->condition->value;
        return "[[$attribute]] IS NOT NULL";
    }
}
