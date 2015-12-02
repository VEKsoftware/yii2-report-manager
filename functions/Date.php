<?php

namespace reportmanager\functions;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is an abstract class containing definition of ReportsConditions Functions.
 *
 */
class Date extends Func
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
        return 'date';
    }

    /**
     * @inherit
     */
    public static function getLabel()
    {
        return Yii::t('reportmanager','Date');
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
        return 'date';
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
        if(Yii::$app->db->driverName === 'mysql')
            return "UNIX_TIMESTAMP([[$attribute]])";
        elseif(Yii::$app->db->driverName === 'pgsql')
            return "EXTRACT(EPOCH from [[$attribute]])";
    }

    /**
     * Prepare output string for rendering Table
     *
     * @return string
     */
    public function prepareTable()
    {
//        if($this->condition->value) {
        $condition = $this->condition;
        return [
            'attribute' => $condition->alias,
            'value' => function($model) use($condition){
                $prop = $condition->alias;
                $format = is_string($condition->value) ? $condition->value : NULL;
                $date = new \DateTime;
                $date->setTimestamp($model->$prop);
                return $date->format($format);
            },
        ];
    }

    public function prepareGraph($val)
    {
        $format = is_string($this->condition->value) ? $this->condition->value : NULL;
        $date = new \DateTime;
        $date->setTimestamp($val);
        $date_formatted = $date->format($format);
        $datetime = \DateTime::createFromFormat('!'.$format,$date_formatted);
        return $datetime ? 1000*$datetime->getTimestamp() : 0;//$val*1000;
    }
}
