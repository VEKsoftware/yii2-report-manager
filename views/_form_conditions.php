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
<?php Pjax::begin([
        'id' => 'conditions',
//        'linkSelector' => '#reports a',
        'enablePushState' => false
]) ?>
<?php $form = ActiveForm::begin(['options' => ['data-pjax' => true ]]); ?>

    <?= $form->errorSummary($dataProvider->allModels); ?>

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
                        .$form->field($model, "[$index]attribute_name")->dropDownList(ArrayHelper::map($report->availableProps,'attribute','label'))->label(false)
                        .$form->field($model, "[$index]id",['template' => '{input}', 'options' => ['tag' => 'span']])->hiddenInput()->label(false)
                        .$form->field($model, "[$index]report_id",['template' => '{input}', 'options' => ['tag' => 'span']])->hiddenInput()->label(false)
                    ;
                },
            ],
            [
                'attribute' => 'operation',
                'format' => 'raw',
                'value' => function($model, $key, $index, $column) use($report,$form){
                    return ''
                        .$form->field($model, "[$index]operation")->dropDownList(ReportsConditions::getOperationsList())->label(false)
                    ;
                },
            ],
            [
                'attribute' => 'function',
                'format' => 'raw',
                'value' => function($model, $key, $index, $column) use($report,$form){
                    return ''
                        .$form->field($model, "[$index]function")->dropDownList(ReportsConditions::getOperationsList())->label(false)
                    ;
                },
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{delete}',
                'buttons' => [
                    'delete' => function ($url, $model, $key) {
                        // <a href="/rep/delete?id=1" title="Удалить" aria-label="Удалить" data-confirm="Вы уверены, что хотите удалить этот элемент?" data-method="post" data-pjax="0"><span class="glyphicon glyphicon-trash"></span></a>
                        return 1 === 1 ? Html::a('<span class="glyphicon glyphicon-trash"></span>', $url,['title' => Yii::t('reportmanager','Delete')]) : '';
                    },
                ]
            ],
        ],
    ]) : ''; ?>
    <?= Html::submitButton(Yii::t('reportmanager', 'Save'), ['class' => 'btn btn-primary']) ?>

    <?= Html::submitButton(Yii::t('reportmanager', 'Add Condition'), ['class' => 'btn btn-primary', 'name' => 'add-condition']) ?>

<?php ActiveForm::end(); ?>
<?php Pjax::end() ?>
