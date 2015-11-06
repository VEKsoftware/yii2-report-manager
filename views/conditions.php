<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\web\JsExpression;

$this->title = Yii::t('reportmanager', 'Conditions');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reports-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('reportmanager', 'Create Condition'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

<div class='row'>
<div class='col-sm-4'>

<?php Pjax::begin([
        'id' => 'conditions',
//        'linkSelector' => '#reports a',
        'enablePushState' => false
]) ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'attribute_name',
                'format' => 'raw',
                'value' => function($model) {
                    return Html::a(Html::encode($model->attribute_name),['index', 'id'  => $model->id],[
                        'data-pjax' => true,
                        'onclick' => new JsExpression('$.pjax({url: "'.Url::toRoute(['update','id' => $model->id]).'", container: "#new_report", push: false});return false;'),
                    ]).Html::tag('p',Yii::$app->formatter->asNtext($model->description));
                },
            ],
            'operation',
            'function',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end() ?>
</div>
<div class='col-sm-8'>
<!-- Render create form -->    
    <?php /*=$this->render('_form', [
        'model' => $model,
    ]) */?>

</div>
</div>
</div>
