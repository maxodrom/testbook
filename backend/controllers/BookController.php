<?php

namespace backend\controllers;

use Yii;
use common\models\Book;
use common\models\BookSearch;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\Html;
use common\components\Util;
use yii\imagine\Image;
use Imagine\Image\Point;
use Imagine\Image\ImageInterface;
use Imagine\Image\Box;

/**
 * BookController implements the CRUD actions for Book model.
 */
class BookController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Book models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel  = new BookSearch();
        if (null !== Yii::$app->request->get('BookSearch')) {
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        } else {
            $dataProvider = new ActiveDataProvider([
                'query' => Book::find()->with(['author']),
            ]);
        }

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Book model.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->renderAjax('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Добавление книги.
     *
     * @return mixed
     * @throws \Exception
     * @throws \yii\base\Exception
     */
    public function actionCreate()
    {
        $model = new Book();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if (null !== ($image = UploadedFile::getInstance($model, 'preview'))) {
                $model->preview = $image;
                if(!$model->validate(['preview'])) {
                    Yii::$app->getSession()->setFlash(
                        'error',
                        Html::errorSummary(
                            $model,
                            [
                                'header' => 'Пожалуйста, исправьте следующие ошибки:',
                                'encode' => false
                            ]
                        )
                    );

                    return $this->redirect(['book/update', 'id' => $model->id]);
                }

                // работаем с новым загруженным изображением
                $uploadsDir    = realpath(Yii::$app->params['booksUploadsDir']);
                $uploadsTmpDir = realpath(Yii::$app->params['frontendUploadsTmpDir']);
                // сохраняем изображение во временную директорию
                $tmpFilename = $uploadsTmpDir . DIRECTORY_SEPARATOR . 'book_preview_' . uniqid() . '.' . $image->getExtension();

                if (false === $image->saveAs($tmpFilename)) {
                    throw new Exception('Невозможно сохранить загруженное изображение для книги в файл: ' . $tmpFilename);
                } else {
                    // создаем вложенные директории
                    $path     = Util::createNestedFolders($uploadsDir, $model->id);
                    $filename = $path . DIRECTORY_SEPARATOR . dechex($model->id) . '.' . $image->getExtension();
                    $previewFilename = $path . DIRECTORY_SEPARATOR . dechex($model->id) .
                                       '_thumb.' .
                                       $image->getExtension();

                    $imagine = Image::getImagine();
                    /** @var $i \Imagine\Gd\Image */
                    $i = $imagine->open($tmpFilename);
                    // если размеры изображения превышают лимиты, оно ресайзится
                    if (
                        $i->getSize()->getWidth() > Yii::$app->params['maxImageWidth'] ||
                        $i->getSize()->getHeight() > Yii::$app->params['maxImageHeight'])
                    {
                        $i->thumbnail(
                            new Box(
                                Yii::$app->params['maxImageWidth'],
                                Yii::$app->params['maxImageHeight']
                            ),
                            ImageInterface::THUMBNAIL_INSET
                        )->save(
                            $filename,
                            ['quality' => 100]
                        );
                    } else {
                        $i->save($filename);
                    }

                    // создаем превьюшку из исходника
                    $i = $imagine->open($tmpFilename);
                    if (
                        $i->getSize()->getWidth() > Yii::$app->params['thumbnailDefaultWidth'] ||
                        $i->getSize()->getHeight() > Yii::$app->params['thumbnailDefaultHeight']
                    ) {
                        $i->thumbnail(
                            new Box(
                                Yii::$app->params['thumbnailDefaultWidth'],
                                Yii::$app->params['thumbnailDefaultHeight']
                            ),
                            ImageInterface::THUMBNAIL_INSET
                        )->save(
                            $previewFilename,
                            ['quality' => 100]
                        );
                    }
                    // удаляем временный файл
                    unlink($tmpFilename);

                    $filename     = str_replace('\\', '/', $filename);
                    $filename     = preg_replace('=^(.*?)(/uploads/books/.*)$=iu', '\\2', $filename);
                    $model->preview = $filename;
                }

                $model->update(false, ['preview']);
            }

            Yii::$app->session->setFlash(
                'success',
                'Новая книга успешно добавлена.'
            );

            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Обновление книги.
     *
     * @param string $id
     *
     * @return mixed
     * @throws \Exception
     * @throws \yii\base\Exception
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oldImagePath = $model->preview;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if (null !== ($image = UploadedFile::getInstance($model, 'preview'))) {
                $model->preview = $image;
                if(!$model->validate(['preview'])) {
                    Yii::$app->getSession()->setFlash(
                        'error',
                        Html::errorSummary(
                            $model,
                            [
                                'header' => 'Пожалуйста, исправьте следующие ошибки:',
                                'encode' => false
                            ]
                        )
                    );

                    // возвращаем путь к изображению, если валидация провалилась
                    $model->preview = $oldImagePath;
                    $model->update(false, ['preview']);
                    return $this->render('update', [
                        'model' => $model
                    ]);
                }

                // удаляем предыдущее изображение
                $oldImageFile = Yii::$app->params['booksUploadsDir'] . $oldImagePath;
                if (is_file($oldImageFile)) {
                    unlink($oldImageFile);
                }

                // работаем с новым загруженным изображением
                $uploadsDir    = realpath(Yii::$app->params['booksUploadsDir']);
                $uploadsTmpDir = realpath(Yii::$app->params['frontendUploadsTmpDir']);
                // сохраняем изображение во временную директорию
                $tmpFilename = $uploadsTmpDir . DIRECTORY_SEPARATOR . 'book_preview_' . uniqid() . '.' . $image->getExtension();

                if (false === $image->saveAs($tmpFilename)) {
                    throw new Exception('Невозможно сохранить загруженное изображение для превью книги в файл: ' . $tmpFilename);
                } else {
                    // создаем вложенные директории
                    $path     = Util::createNestedFolders($uploadsDir, $model->id);
                    $filename = $path . DIRECTORY_SEPARATOR . dechex($model->id) . '.' . $image->getExtension();
                    $previewFilename = $path . DIRECTORY_SEPARATOR . dechex($model->id) .
                                       '_thumb.' .
                                       $image->getExtension();

                    $imagine = Image::getImagine();
                    /** @var $i \Imagine\Gd\Image */
                    $i = $imagine->open($tmpFilename);
                    // если размеры изображения превышают лимиты, оно ресайзится
                    if (
                        $i->getSize()->getWidth() > Yii::$app->params['maxImageWidth'] ||
                        $i->getSize()->getHeight() > Yii::$app->params['maxImageHeight'])
                    {
                        $i->thumbnail(
                            new Box(
                                Yii::$app->params['maxImageWidth'],
                                Yii::$app->params['maxImageHeight']
                            ),
                            ImageInterface::THUMBNAIL_INSET
                        )->save(
                            $filename,
                            ['quality' => 100]
                        );
                    } else {
                        $i->save($filename);
                    }

                    // создаем превьюшку из исходника
                    $i = $imagine->open($tmpFilename);
                    if (
                        $i->getSize()->getWidth() > Yii::$app->params['thumbnailDefaultWidth'] ||
                        $i->getSize()->getHeight() > Yii::$app->params['thumbnailDefaultHeight']
                    ) {
                        $i->thumbnail(
                            new Box(
                                Yii::$app->params['thumbnailDefaultWidth'],
                                Yii::$app->params['thumbnailDefaultHeight']
                            ),
                            ImageInterface::THUMBNAIL_INSET
                        )->save(
                            $previewFilename,
                            ['quality' => 100]
                        );
                    }
                    // удаляем временный файл
                    unlink($tmpFilename);

                    $filename     = str_replace('\\', '/', $filename);
                    $filename     = preg_replace('=^(.*?)(/uploads/books/.*)$=iu', '\\2', $filename);
                    $model->preview = $filename;
                }
            } else {
                $model->preview = $oldImagePath;
            }

            $model->update(false, ['preview']);

            Yii::$app->getSession()->setFlash(
                'success',
                'Данные книги &laquo;' . $model->name . '&raquo; успешно обновлены.'
            );

            if (null === $referer = Yii::$app->request->post('referer')) {
                return $this->redirect(['index', 'id' => $model->id]);
            } else {
                return $this->redirect($referer);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Удаление книги.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        Yii::$app->session->setFlash(
            'success',
            'Запись о книге успещно удалена.'
        );

        return $this->redirect(['index']);
    }

    /**
     * Finds the Book model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $id
     *
     * @return Book the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Book::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
