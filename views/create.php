<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model reportmanager\models\Reports */

$this->title = Yii::t('reportmanager', 'Create Report');
$this->params['breadcrumbs'][] = ['label' => Yii::t('reportmanager', 'Reports'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reports-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
