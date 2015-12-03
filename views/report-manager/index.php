<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('reportmanager', 'Reports');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reports-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('reportmanager', 'Create Report'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'name',
                'format' => 'html',
                'value' => function($model) {
                    return $model->isAllowed('view') ?
                        Html::a(Html::encode($model->name),['view', 'id' => $model->id]).Html::tag('p',Yii::$app->formatter->asNtext($model->description)) :
                        Html::encode($model->name);
                },
            ],
            'creator.name',
            'group.name',
/*
            [
                'attribute' => 'creator_id',
                'format' => 'html',
                'value' => function($model) {
                    return Html::a($model->name,[])
                }
            ]
*/
            [
                'class' => 'yii\grid\ActionColumn',
                'buttons'=>[
                    'update' => function ($url, $model, $key) {
                        return $model->isAllowed('update') ?
                            Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['update','id'=>$model->id], ['title' => Yii::t('yii', 'Update')])
                            :'';
                    },
                    'view' => function ($url, $model, $key) {
                        return $model->isAllowed('view') ?
                            Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['view','id'=>$model->id], ['title' => Yii::t('yii', 'View')])
                            :'';
                    },
                    'delete' => function ($url, $model, $key) {
                        return $model->isAllowed('delete') ?
                            Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete','id'=>$model->id], [
                                'title' => Yii::t('yii', 'Delete'),
                                'aria-label' => Yii::t('yii', 'Delete'),
                                'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                'data-method' => 'post',
                                'data-pjax' => '0',
                                ])
                            :'';
                    },
                        ],
                ],
        ],
    ]); ?>

</div>
