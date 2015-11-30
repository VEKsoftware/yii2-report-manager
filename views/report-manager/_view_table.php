<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use reportmanager\models\ClassSearch;

/* @var $this yii\web\View */
/* @var $model reportmanager\models\Reports */

//var_dump($model->columns);
//die();
?>
<div class="reports-view-table">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'showHeader' => false,
//        'rowOptions' => function($model, $key, $index, $grid) {
//            return ['data-index' => $index];
//        },
        'columns' => array_merge([
//            ['class' => 'yii\grid\SerialColumn'],
        ],ArrayHelper::getColumn($model->columns,function($item){
            return $item->functionObj->prepareTable();
        })),
    ]) ?>
</div>
