<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

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
    <?= $this->render('_form_conditions', [
        'dataProvider' => $condDataProvider,
        'report' => $report,
        'form' => $form,
    ]) ?>
  </div>
  </div>
<?php ActiveForm::end(); ?>
    <?php Pjax::end() ?>
</div>
