<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;
use reportmanager\models\ReportsConditions;

$this->title = Yii::t('reportmanager', 'Conditions');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reports-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= isset($report->id)? Html::a(Yii::t('reportmanager', 'Add Condition'), ['create'], ['class' => 'btn btn-success', 'id' => 'add_condition']) : '' ?>
    </p>

<?php Pjax::begin([
        'id' => 'conditions',
//        'linkSelector' => '#reports a',
        'enablePushState' => false
]) ?>
<?php $form = ActiveForm::begin(['options' => ['data-pjax' => true ]]); ?>

    <?= GridView::widget([
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
            'function',

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
    ]); ?>
    <?= Html::submitButton(Yii::t('reportmanager', 'Save'), ['class' => 'btn btn-primary']) ?>
<?php ActiveForm::end(); ?>
<?php Pjax::end() ?>
</div>
<?php $this->registerJs('
    $("#add_condition").click(function(event) {
        event.preventDefault();
        $.pjax.reload({container:"#conditions", type: "POST", push: false, replace: false});  //Reload GridView
/*
        var maxValue = Math.max.apply(Math,$(\'#table_conditions tbody tr\').map(function(){ return this.getAttribute("data-index");}));
        var number = maxValue + 1;
        var col1 = '
            .json_encode(
                Html::dropDownList('ReportsConditions[_XXX_][attribute_name]',NULL, ArrayHelper::map($report->availableProps,'attribute','label'),[
                    'class' => 'form-control',
                    'id' => 'reportsconditions-_XXX_-attribute_name',
                ])
            ).';
        var col2 = '
            .json_encode(
                Html::dropDownList('ReportsConditions[_XXX_][operation]',NULL, ReportsConditions::getOperationsList(),['class' => 'form-control'])
            ).';
        var html = \'<tr data-index="\' + number + \'">\' +
                \'<td>\' + col1 + \'</td>\' +
                \'<td>\' + col2 + \'</td>\' +
                \'<td></td>\' +
                \'<td></td>\' +
                \'</td>\';
        $("#table_conditions tbody").append(html.replace(/_XXX_/g,number));
*/
    })
')?>
