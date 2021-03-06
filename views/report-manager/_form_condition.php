<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use reportmanager\models\ReportsConditions;

?>

    <?= $form->field($model, "id",['template' => '{input}', 'options' => ['tag' => 'span', 'class' => '']])->hiddenInput() ?>

    <?= $form->field($model, "report_id",['template' => '{input}', 'options' => ['tag' => 'span', 'class' => '']])->hiddenInput() ?>

    <?php if($model->operation === 'select'): ?>
    <?= $form->field($model, "col_label")->textInput() ?>
    <?php endif ?>

    <?= $form->field($model, "operation")->dropDownList(ReportsConditions::getOperationsList()) ?>

    <?= $form->field($model, "attribute_name")->dropDownList(ArrayHelper::map($model->report->availableProps,'attribute','label')) ?>

    <?php if(isset($model->operation)): ?>

        <?= $form->field($model, "function")->dropDownList(
            array_map(function($v){ return $v::getLabel(); },
                ReportsConditions::getFunctionsList($model->operation)
            ),
            [
                'prompt' => Yii::t('reportmanager','Select...'),
            ]
        ) ?>

        <?php if($model->functionObj->paramType): ?>

            <?php if(isset($model->config['values']) && is_array($model->config['values'])): ?>
                <?= $form->field($model, "value")->dropDownList($model->config['values'],
                    [
                        'prompt' => Yii::t('reportmanager','Select...'),
                        'multiple' => true,
                        'size' => 16,
                    ]) ?>
            <?php else: ?>
                <?= $form->field($model, "value")->textInput() ?>
            <?php endif ?>

        <?php endif /* $model->functionObj->paramType */?>

    <?php endif /* $model->operation */?>

    <?= Html::submitButton($model->isNewRecord ? Yii::t('reportmanager', 'Create') : Yii::t('reportmanager', 'Save'), [
        'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
        'data' => [
            'pjax' => true,
        ],
        'name' => 'save',
    ]) ?>

    <?= Html::a(Yii::t('reportmanager', 'Delete'), ['delete-condition','id' =>$model->id ],[
        'class' => 'btn btn-danger',
        'title' => Yii::t('reportmanager','Delete'),
        'aria-label' => Yii::t('reportmanager', 'Delete'),
        'data' => [
            'confirm' => Yii::t('reportmanager','Are you sure want to delete this item?'),
            'method' => 'post',
//            'params' => ['delete' => $model->id],
        ],
    ]) ?>

