<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\ListView;
use reportmanager\models\ReportsConditions;

?>

<?= Html::a(Yii::t('reportmanager', 'Add Condition'),['condition','report_id' => $model->id], ['class' => 'btn btn-primary', 'name' => 'add-condition']) ?>

<?= ListView::widget([
    'dataProvider' => $condDataProvider,
    'itemOptions' => ['class' => 'list'],
//    'itemView' => '_view_condition',
//*
    'itemView' => function ($model, $key, $index, $widget) use($condition){
//        $func = ReportsConditions::getFunctionsList($model->operation,$model->function);
        $func = $model->currentFunction;
        $param = NULL;
        if(isset($model->currentFunction) && isset($model->currentFunction['param'])) {
            if(is_array($model->value)) {
                $all_values = $model->config['values'];
                $values = array_map(function($v) use($all_values){
                    return $all_values[$v];
                }, $model->value);
                $param = join(', ',$values);
            } else {
                $param = $model->value;
            }
        }
        $title = ''
            .$model->operationsList[$model->operation]
            .': '
            .$model->conditionLabel
            .' '
            .(is_array($func) ? $func['label'] : '')
            .' '
            .($param ? $param : '')
        ;

        return isset($condition) && $condition->id == $model->id ?
            Html::tag('span',$title) :
            Html::a($title, ['condition','report_id' => $model->report->id,'id' => $model->id])
        ;
    },
//*/
]) ?>