<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use reportmanager\models\ReportsConditions;

?>

    <?= $form->field($model, "[$index]id",['template' => '{input}', 'options' => ['tag' => 'span', 'class' => '']])->hiddenInput() ?>

    <?= $form->field($model, "[$index]report_id",['template' => '{input}', 'options' => ['tag' => 'span', 'class' => '']])->hiddenInput() ?>

    <?= $form->field($model, "[$index]attribute_name")->dropDownList(ArrayHelper::map($model->report->availableProps,'attribute','label')) ?>

    <?= $form->field($model, "[$index]operation")->dropDownList(ReportsConditions::getOperationsList()) ?>

    <?php if(isset($model->operation)): ?>

        <?= $form->field($model, "[$index]function")->dropDownList(
            array_map(function($v){ if(!isset($v['label'])) { var_dump($v);die(); }; return $v['label']; },ReportsConditions::getFunctionsList($model->operation))
            , ['prompt' => Yii::t('reportmanager','Select...')]
        ) ?>

        <?php if(isset($model->currentFunction) && isset($model->currentFunction['param'])): ?>

            <?php if(isset($model->config['values']) && is_array($model->config['values'])): ?>
                <?= $form->field($model, "[$index]param")->dropDownList($model->config['values'],
                    ['prompt' => Yii::t('reportmanager','Select...'),'multiple' => true]) ?>
            <?php else: ?>
                <?= $form->field($model, "[$index]param")->textInput() ?>
            <?php endif ?>

        <?php endif /* $model->currentFunction */?>

    <?php endif /* $model->operation */?>
