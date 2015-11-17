<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\ListView;
use reportmanager\models\ReportsConditions;

?>

<?= ListView::widget([
    'dataProvider' => $condDataProvider,
    'itemOptions' => ['class' => 'item'],
//    'itemView' => '_view_condition',
//*
    'itemView' => function ($model, $key, $index, $widget) {
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
        return Html::a(''
            .$model->operationsList[$model->operation]
            .' '
            .$model->attributeLabel
            .' '
            .(is_array($func) ? $func['label'] : '')
            .' '
            .($param ? $param : '')
        ,['condition','id' => $model->id]);
    },
//*/
]) ?>