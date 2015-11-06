<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\web\JsExpression;

$this->title = Yii::t('reportmanager', 'Reports');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reports-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('reportmanager', 'Create Report'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

<div class='row'>
<div class='col-sm-4'>

<?php Pjax::begin([
        'id' => 'reports',
        'linkSelector' => '#reports a',
        'enablePushState' => false
]) ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function($model) {
                    return Html::a(Html::encode($model->name),['index', 'id'  => $model->id],[
                        'data-pjax' => true,
                        'onclick' => new JsExpression('$.pjax({url: "'.Url::toRoute(['update','id' => $model->id]).'", container: "#new_report", push: false});return false;'),
                    ]).Html::tag('p',Yii::$app->formatter->asNtext($model->description));
                },
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end() ?>
</div>
<div class='col-sm-8'>
<!-- Render create form -->    
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
</div>
</div>
