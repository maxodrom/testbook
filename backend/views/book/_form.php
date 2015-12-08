<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\Author;
use yii\helpers\ArrayHelper;
use dosamigos\datepicker\DatePicker;

/* @var $this yii\web\View */
/* @var $model common\models\Book */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="book-form">

    <?php $form = ActiveForm::begin([
        'layout' => 'default',
        'options' => ['enctype' => 'multipart/form-data']
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'author_id')->dropDownList(
        ArrayHelper::map(Author::find()->orderBy(['lastname' => SORT_ASC])->asArray()->all(), 'id', function ($model, $defaultValue) {
            return $model['lastname'] . ' ' . $model['firstname'];
        }),
        ['prompt' => '---выберите автора---']
    ) ?>

    <?php $model->date = preg_replace('/(\d{4})-(\d{2})-(\d{2})/', '\\3.\\2.\\1', $model->date); ?>
    <?= $form->field($model, 'date')->hint('пример: 04.12.1975 (число.месяц.год)')->widget(
        DatePicker::className(), [
            'template' => '{addon}{input}',
            'language' => 'ru',
            'clientOptions' => [
                'autoclose' => true,
                'format' => 'dd.mm.yyyy',
                'todayHighlight' => true,
            ],
        ]
    ); ?>

    <?php if($model->preview != null): ?>
        <div class="form-group">
            <?= Html::img(
                Yii::$app->params['frontendAbsoluteURL'] .
                $model->preview,
                ['id' => 'preview', 'class' => 'img-rounded']
            ) ?>
        </div>
    <?php endif; ?>

    <?= $form->field($model, 'preview')->fileInput() ?>

    <?= Html::hiddenInput('referer', null !== Yii::$app->request->getReferrer() ? Yii::$app->request->getReferrer() : null) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Обновить',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
