<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model reportmanager\models\Reports */

$this->title = Yii::t('reportmanager', 'Create Report');
$this->params['breadcrumbs'][] = ['label' => Yii::t('reportmanager', 'Reports'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reports-create">

    <h1><?= Html::encode($this->title) ?></h1>

<?php $form = ActiveForm::begin(['options' => ['data-pjax' => true ]]); ?>

    <?= $this->render('_form', [
        'model' => $model,
        'form' => $form,
    ]) ?>

<?php ActiveForm::end(); ?>

</div>
