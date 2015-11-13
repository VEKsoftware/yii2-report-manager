<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
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
        <?= Html::a(Yii::t('reportmanager', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('reportmanager', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('reportmanager', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'description:ntext',
        ],
    ]) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'showHeader' => false,
//        'rowOptions' => function($model, $key, $index, $grid) {
//            return ['data-index' => $index];
//        },
        'columns' => array_merge([
            ['class' => 'yii\grid\SerialColumn'],
        ],ClassSearch::$dynamic_attributes),
    ]) ?>
</div>
