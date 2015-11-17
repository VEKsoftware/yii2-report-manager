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
        'formSelector' => 'form',
        'scrollTo' => false,
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
    <?= $this->render('_list_conditions', [
        'model' => $report,
        'condDataProvider' => $condDataProvider,
        'form' => $form,
    ]) ?>

  </div>
  </div>
<?php $this->registerJs('
$("form[data-pjax]").on("change",function(event){
    $.pjax.submit(event, "#all",{scrollTo: false});
});
') ?>

<?php ActiveForm::end(); ?>
    <?php Pjax::end() ?>
</div>
