<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Employees */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="employees-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'date_prev')->textInput() ?>

    <?= $form->field($model, 'date_next')->textInput() ?>

    <?= $form->field($model, 'in_office')->checkbox([
        'label' => Yii::t('forms', 'In office'),
        'labelOptions' => [
            'style' => 'padding-left:20px;'
        ],
        'disabled' => false
    ]);?>

    <?= $form->field($model, 'postponement')->checkbox([
        'label' => Yii::t('forms', 'Postponement'),
        'labelOptions' => [
            'style' => 'padding-left:20px;'
        ],
        'disabled' => false
    ]);?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
