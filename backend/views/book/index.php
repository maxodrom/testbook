<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use himiklab\colorbox\Colorbox;
use yii\web\View;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\BookSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = 'Книги';
$this->params['breadcrumbs'][] = $this->title;

$monthes = [
    1  => 'января',
    2  => 'февраля',
    3  => 'марта',
    4  => 'апреля',
    5  => 'мая',
    6  => 'июня',
    7  => 'июля',
    8  => 'августа',
    9  => 'сентября',
    10 => 'октября',
    11 => 'ноября',
    12 => 'декабря'
];

/*$this->registerAssetBundle(ColorboxAsset::className());
$this->registerJs(
    "
        $('.cbox').colorbox();
    ",
    View::POS_READY
);*/
?>

<?= Colorbox::widget([
    'targets' => [
        '.cbox' => [
            'maxWidth' => 800,
            'maxHeight' => 600,
        ],
    ],
    'coreStyle' => 3
]) ?>
<div class="book-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить книгу', ['create'],
            ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns'      => [
            'id',
            'name',
            [
                'attribute' => 'preview',
                'format'    => 'raw',
                'value'     => function ($model) {
                    if ($model->preview != null) {
                        return Html::a(
                            Html::img(
                                Yii::$app->params['frontendAbsoluteURL'] .
                                preg_replace('~^(.*?)(\d+)\.(jpe?g|png|gif)$~iu',
                                    '$1$2_thumb.$3', $model->preview)
                            ),
                            Yii::$app->params['frontendAbsoluteURL'] . $model->preview,
                            [
                                'class' => 'cbox'
                            ]
                        );
                    } else {
                        return '&mdash;';
                    }
                }
            ],
            [
                'attribute' => 'author_id',
                'format'    => 'html',
                'value'     => function ($model) {
                    return $model->author->firstname . ' ' . $model->author->lastname;
                }
            ],
            [
                'attribute' => 'date',
                'format'    => 'raw',
                'value'     => function ($model) use ($monthes) {
                    $t = strtotime($model->date);
                    return (int) date('d', $t) . ' ' . $monthes[date('n', $t)] . ' ' . date('Y', $t);
                }
            ],
            [
                'attribute' => 'date_update',
                'format' => 'raw',
                'value' => function ($model) use ($monthes) {
                    $t = $model->date_update;
                    return (int) date('d', $t) . ' ' . $monthes[date('n', $t)] . ' ' . date('Y', $t);
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Кнопки действий',
                'template' => '<div class="btn-group">{view}{update}{delete}</div>',
                'buttons' => [
                    'view' => function($url, $model, $key) {
                        return Html::a(
                            FA::icon(FA::_EYE),
                            Url::to([
                                'book/view', 'id' => $model->id
                            ]),
                            [
                                'class' => 'btn btn-default',
                                'title' => 'просмотр книги'
                            ]
                        );
                    },
                    'update' => function($url, $model, $key) {
                        return Html::a(
                            FA::icon(FA::_PENCIL),
                            Url::to([
                                'book/update',
                                'id' => $model->id
                            ]),
                            [
                                'class' => 'btn btn-default',
                                'title' => 'редактирование книги'
                            ]);
                    },
                    'delete' => function($url, $model, $key) {
                        return Html::a(
                            FA::icon(FA::_TRASH),
                            Url::to([
                                'book/delete',
                                'id' => $model->id
                            ]),
                            [
                                'class' => 'btn btn-danger',
                                'title' => 'удаление книги',
                                'data-method' => "post"
                            ]
                        );
                    }
                ],
                'headerOptions' => [
                    'style' => 'width:210px;'
                ]
            ],
        ],
    ]); ?>

</div>
