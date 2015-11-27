<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use reportmanager\models\ClassSearch;

/* @var $this yii\web\View */
/* @var $model reportmanager\models\Reports */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('reportmanager', 'Reports'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reports-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= $model->isAllowed('update') ? Html::a(Yii::t('reportmanager', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) : '' ?>
        <?= $model->isAllowed('delete') ? Html::a(Yii::t('reportmanager', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('reportmanager', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) : '' ?>
    </p>

    <div class="reports-view-description">

    <div class="row">
        <div class="col-sm-6">
            <label><?= Yii::t('reportmanager','Creator') ?>:</label>
            <?=Html::encode(ArrayHelper::getValue($model,'creator.name'))?>
        </div>
        <div class="col-sm-6">
            <label><?= Yii::t('reportmanager','Access Group') ?>:</label>
            <?=Html::encode(ArrayHelper::getValue($model,'group.name'))?>
        </div>
    </div>
<label><?=Yii::t('reportmanager','Description')?>:</label>
<div>
<?=Yii::$app->formatter->asNText($model->description)?>
</div>
    </div>

    <?php if(in_array($model->show,['graph','both'])): ?>
        <?=$this->render('_view_graph',[
            'model' => $model,
            'dataProvider' => $dataProvider,
        ])?>
    <?php endif ?>

    <?php if(in_array($model->show,['table','both'])): ?>
        <?=$this->render('_view_table',[
            'model' => $model,
            'dataProvider' => $dataProvider,
        ])?>
    <?php endif ?>

</div>
