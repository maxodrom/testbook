<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Author;
use dosamigos\datepicker\DatePicker;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\BookSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="book-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>


    <div class="row">
        <div class="col-lg-6">
            <?php echo $form->field($model, 'author_id')->label(false)->dropDownList(
                ArrayHelper::map(Author::find()->orderBy(['lastname' => SORT_ASC])->asArray()->all(), 'id', function ($model, $defaultValue) {
                    return $model['lastname'] . ' ' . $model['firstname'];
                }),
                ['prompt' => '---автор---']
            ) ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'name')->label(false)->textInput(['maxwidth' => true, 'placeholder' => 'название книги']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3">
            Дата выход книги:
            <?= DatePicker::widget([
                'name' => 'BookSearch[from_date]',
                'value' => null !== Yii::$app->request->get('BookSearch')['from_date'] ? Yii::$app->request->get('BookSearch')['from_date'] : '',
                'template' => '{addon}{input}',
                'language' => 'ru',
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'dd.mm.yyyy',
                    'todayHighlight' => true,
                ]
            ]);?>
        </div>
        <div class="col-lg-3">
            до
            <?= DatePicker::widget([
                'name' => 'BookSearch[to_date]',
                'value' => null !== Yii::$app->request->get('BookSearch')['to_date'] ? Yii::$app->request->get('BookSearch')['to_date'] : '',
                'template' => '{addon}{input}',
                'language' => 'ru',
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'dd.mm.yyyy',
                    'todayHighlight' => true,
                ]
            ]);?>
        </div>
        <div class="col-lg-4">
            <div>&nbsp;</div>
            <div class="form-group">
                <?= Html::submitButton('Найти', ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Сброс',Url::to(['/book']), ['class' => 'btn btn-default']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
