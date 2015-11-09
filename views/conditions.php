<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;

$this->title = Yii::t('reportmanager', 'Conditions');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reports-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('reportmanager', 'Add Condition'), ['create'], ['class' => 'btn btn-success', 'id' => 'add_condition']) ?>
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
        'columns' => [
            [
                'attribute' => 'attribute_name',
                'format' => 'raw',
                'value' => function($model, $key, $index, $column) use($report){
                    return ''
                    .Html::activeHiddenInput($report,"condition[$index][id]",['value' => $model->id])
                    .Html::activeHiddenInput($report,"condition[$index][report_id]",['value' => $model->report_id])
                    .Html::activeDropDownList($report,"condition[$index][attribute_name]",
                        ArrayHelper::map($report->availableProps,'attribute','label'),
                        ['class' => 'form-control']
                    );
                },
            ],
            'operation',
            'function',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?= Html::submitButton(Yii::t('reportmanager', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
<?php ActiveForm::end(); ?>
<?php Pjax::end() ?>
</div>
<?php $this->registerJs('
    $("#add_condition").click(function(event) {
        event.preventDefault();
        var name = "'.Html::getInputName($report,'condition').'[" + $(\'#table_conditions tbody tr\').length + "]";
        var col1 = '
            .json_encode(
                Html::dropDownList('_NAME_[attribute_name]',NULL, ArrayHelper::map($report->availableProps,'attribute','label'),['class' => 'form-control'])
            ).';
        $("#table_conditions tbody").append(\'<tr> <td>\' + col1.replace("_NAME_",name) + \'</td> <td>н</td> <td></td> <td></td> </td>\');
    })
')?>
