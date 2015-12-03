<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\ListView;
use yii\web\JsExpression;
use reportmanager\models\ReportsConditions;

?>

<?= Html::a(Yii::t('reportmanager', 'Add Condition'),['condition','report_id' => $model->id], ['class' => 'btn btn-primary', 'name' => 'add-condition']) ?>

<?= ListView::widget([
    'dataProvider' => $condDataProvider,
    'itemOptions' => ['class' => 'list'],
//    'itemView' => '_view_condition',
//*
    'itemView' => function ($model, $key, $index, $widget) use($condition){
        $param = NULL;
        if($model->functionObj->paramType) {
            if(is_array($model->value)) {
                $all_values = $model->config['values'];
                $values = array_map(function($v) use($all_values){
                    return array_key_exists($v,$all_values) ? $all_values[$v] : NULL;
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
            .$model->functionObj->label
            .' '
            .($param ? $param : '')
        ;

        return ''
            .Html::a('▲',['condition','report_id' => $model->report->id,'id' => $model->id],[
                'data' => [
                    'operation' => 'up',
                ],
            ])
            .Html::a('▼',['condition','report_id' => $model->report->id,'id' => $model->id],[
                'data' => [
                    'operation' => 'down',
                ],
            ])
            .(isset($condition) && $condition->id == $model->id ?
                Html::tag('span',$title) :
                Html::a($title, ['condition','report_id' => $model->report->id,'id' => $model->id])
            )
        ;
    },
//*/

]) ?>

<?php

$this->registerJs('
if ($.support.pjax) {
    $(document).on("click", "a[data-operation]", function(event) {
        $.pjax.click(event, {type: "post", push: false, container: "#all", data: {operation: $(event.currentTarget).attr("data-operation")}})
    })
}
');

?>