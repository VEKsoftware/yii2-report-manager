<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model reportmanager\models\Reports */

$this->title = Yii::t('reportmanager', 'Update Report: ') . ' ' . $report->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('reportmanager', 'Reports'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $report->name, 'url' => ['view', 'id' => $report->id]];
$this->params['breadcrumbs'][] = Yii::t('reportmanager', 'Update');
?>
<div class="reports-update">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin([
        'id' => 'all',
//        'linkSelector' => '#reports a',
//        'enablePushState' => false,
        'formSelector' => 'form',
        'clientOptions' => [
//            'async' => true,
        ],
    ]) ?>
<?php $form = ActiveForm::begin(['options' => ['data-pjax' => true ]]); ?>

  <div class="row">
  <div class="col-sm-4">

    <?= $this->render('_form', [
        'model' => $report,
        'form' => $form,
    ]) ?>
  </div>

  <div class="col-sm-8">
    <?= isset($report->id)? GridView::widget([
        'dataProvider' => $condDataProvider,
        'id' => 'table_conditions',
        'showHeader' => false,
        'rowOptions' => function($model, $key, $index, $grid) {
            return ['data-index' => $index];
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'attribute_name',
                'format' => 'raw',
                'value' => function($model, $key, $index) use($report, $form){
                    return $this->render('_form_condition', [
                        'model' => $model,
                        'report' => $report,
                        'form' => $form,
                        'index' => $index,
                    ]);
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

  </div>
  </div>
<?php $this->registerJs('
$("form[data-pjax]").on("change",function(event){
    $.pjax.submit(event, "#all");
});
') ?>

<?php ActiveForm::end(); ?>
    <?php Pjax::end() ?>
</div>
