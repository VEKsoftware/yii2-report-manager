<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use reportmanager\models\Reports;

/* @var $this yii\web\View */
/* @var $model reportmanager\models\Reports */
/* @var $form yii\widgets\ActiveForm */
?>

<?php
$this->registerJs(
   '$("document").ready(function(){
        $("#new_report").on("pjax:end", function() {
            $.pjax.reload({container:"#reports"});  //Reload GridView
        });
    });'
);
?>

<?php yii\widgets\Pjax::begin([
        'id' => 'new_report',
        'enablePushState' => false,
//        'enableReplaceState' => true,
]) ?>
<?php $form = ActiveForm::begin(['options' => ['data-pjax' => true ]]); ?>

<div class="row">
    <div class="reports-form col-sm-4">

        <?= $form->field($model, 'id')->textInput() ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'class_name')->dropDownList(ArrayHelper::map(Reports::$classes_list,'class','label'),['maxlength' => true]) ?>

        <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    </div>
    <div class="reports-form-fields col-sm-8">
        <?= Html::button('+',['id' => 'add_prop']) ?>
        <?php $this->registerJs('
            $(document).ready(function(e) {
                var $table = $("#props table tbody");
                $("#add_prop").click(function(e) {
                    var html = \'<tr> <td>Added</td> <td>Added</td> </tr>\';
                    $table.append(html);
                    return false;
                });
            });
        ')?>

        <?=GridView::widget([
            'dataProvider' => new ArrayDataProvider(['allModels' => $model->availableProps]),
            'showHeader' => false,
            'id' => 'props',
            'summary' => '',
            'columns' => [
                'label',
                [
                    'label' => Yii::t('reportmanager','Operations'),
                    'format' => 'raw',
                    'value' => function($item) use($model) {
                        return Html::activeDropDownList($model,'test',['x'=>'y','z'=>'h']);
                    },
                ],
            ],
        ])?>
    </div>

</div>

<div class="form-group">
    <?= Html::submitButton($model->isNewRecord ? Yii::t('reportmanager', 'Create') : Yii::t('reportmanager', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>
<?php yii\widgets\Pjax::end() ?>

