<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model reportmanager\models\Reports */

$this->title = Yii::t('reportmanager', 'Update Report: ') . ' ' . $report->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('reportmanager', 'Reports'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $report->name, 'url' => ['view', 'id' => $report->id]];
$this->params['breadcrumbs'][] = Yii::t('reportmanager', 'Update');
?>
<div class="reports-update">
    <h1><?= Html::encode($this->title) ?></h1>

  <div class="row">
  <div class="col-sm-4">
    <?= $this->render('_form', [
        'model' => $report,
    ]) ?>
  </div>

  <div class="col-sm-8">
    <?php Pjax::begin([
        'id' => 'conditions',
//        'linkSelector' => '#reports a',
//        'enablePushState' => false
    ]) ?>
    <?= $this->render('_form_conditions', [
        'dataProvider' => $condDataProvider,
        'report' => $report,
    ]) ?>
    <?php Pjax::end() ?>
  </div>


  </div>
</div>
