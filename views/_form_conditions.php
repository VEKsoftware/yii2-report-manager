<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;
use reportmanager\models\ReportsConditions;

?>

    <?= isset($report->id)? GridView::widget([
        'dataProvider' => $dataProvider,
        'id' => 'table_conditions',
        'rowOptions' => function($model, $key, $index, $grid) {
            return ['data-index' => $index];
        },
        'columns' => [
            [
                'attribute' => 'attribute_name',
                'format' => 'raw',
                'value' => function($model, $key, $index, $column) use($report,$form){
                    return ''
                        .$form->field($model, "[$index]attribute_name")->dropDownList(ArrayHelper::map($model->report->availableProps,'attribute','label'))->label(false)
                        .$form->field($model, "[$index]id",['template' => '{input}', 'options' => ['tag' => 'span']])->hiddenInput()->label(false)
                        .$form->field($model, "[$index]report_id",['template' => '{input}', 'options' => ['tag' => 'span']])->hiddenInput()->label(false)
                    ;
                },
            ],
            [
                'attribute' => 'operation',
                'format' => 'raw',
                'value' => function($model, $key, $index, $column) use($form){
                    return ''
                        .$form->field($model, "[$index]operation")->dropDownList(ReportsConditions::getOperationsList())->label(false)
                    ;
                },
            ],
            [
                'attribute' => 'function',
                'format' => 'raw',
                'value' => function($model, $key, $index, $column) use($form){
                    return ''
                        .$form->field($model, "[$index]function")->dropDownList(ReportsConditions::getOperationsList())->label(false)
                    ;
                },
            ],

            [
                'attribute' => 'value',
                'format' => 'raw',
                'value' => function($model, $key, $index, $column) use($report,$form){
                    
                    return ''
                        .$form->field($model, "[$index]value")->dropDownList(['x'=>'y'])->label(false)
                    ;
                },
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{delete}',
                'buttons' => [
                    'delete' => function ($url, $model, $key) use($report) {
                        if($model->id) {
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['update','id' =>$report->id ],[
                                'title' => Yii::t('reportmanager','Delete'),
                                'aria-label' => Yii::t('reportmanager', 'Delete'),
                                'data' => [
                                    'confirm' => Yii::t('reportmanager','Are you sure want to delete this item?'),
                                    'method' => 'post',
                                    'pjax' => false,
                                    'params' => ['delete' => $model->id],
                                ],
                            ]);
                        } else {
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', '#',[
                                'title' => Yii::t('reportmanager','Delete'),
                                'aria-label' => Yii::t('reportmanager', 'Delete'),
                                'onclick' => new JsExpression('$(this).parents("tr").remove(); return false;'),
                            ]);
                        }
                    },
                ]
            ],
        ],
    ]) : ''; ?>
    <?= Html::submitButton(Yii::t('reportmanager', 'Add Condition'), ['class' => 'btn btn-primary', 'name' => 'add-condition']) ?>
