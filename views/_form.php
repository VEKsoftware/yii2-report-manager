<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use reportmanager\models\Reports;

/* @var $this yii\web\View */
/* @var $model reportmanager\models\Reports */
/* @var $form yii\widgets\ActiveForm */
?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'class_name')->dropDownList(
            array_combine(Reports::$classes_list,array_map(function($v){ return $v::getModelLabel(); },Reports::$classes_list)),['maxlength' => true]) ?>

        <?= $form->field($model, 'group_id')->dropDownList($model->getGroupList()) ?>

        <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

<div class="form-group">
    <?= Html::submitButton($model->isNewRecord ? Yii::t('reportmanager', 'Create') : Yii::t('reportmanager', 'Save'), [
        'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
        'data' => [
            'pjax' => true,
        ],
        'name' => 'save-report',
    ]) ?>
</div>

