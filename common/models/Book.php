<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%books}}".
 *
 * @property integer $id
 * @property string  $name
 * @property integer $date_create
 * @property integer $date_update
 * @property string  $preview
 * @property string  $date
 * @property integer $author_id
 *
 * @property Author  $author
 */
class Book extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%books}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class'              => TimestampBehavior::className(),
                'createdAtAttribute' => 'date_create',
                'updatedAtAttribute' => 'date_update',
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                'name',
                'required',
                'message' => 'название книги должно быть указано'
            ],
            [
                'name',
                'filter',
                'filter' => 'trim'
            ],
            [
                'name',
                'string',
                'max'     => 255,
                'tooLong' => 'название книги не должно превышать 255 символов'
            ],
            [
                'author_id',
                'required',
                'message' => 'необходимо указать автора книги'
            ],
            [
                'author_id',
                'integer'
            ],
            [
                'author_id',
                'exist',
                'targetClass'     => Author::className(),
                'targetAttribute' => 'id',
                'message'         => 'такого автора не существует'
            ],
            [
                'preview',
                'default',
                'value' => null
            ],
            [
                'preview',
                'file',
                'extensions'     => ['png', 'jpg', 'gif', 'jpeg'],
                'maxSize'        => isset(Yii::$app->params['maxImageSize']) ? abs(intval(Yii::$app->params['maxImageSize'])) : 1024 * 1024 * 5,
                'tooBig'         => 'загружаемое изображение не должно превышать 5 Мб.',
                'wrongExtension' => 'разрешенные расширения изображений: png, jpg, jpeg, gif'
            ],
            [
                'preview',
                'image',
                'minWidth'    => Yii::$app->params['thumbnailDefaultWidth'],
                'minHeight'   => Yii::$app->params['thumbnailDefaultHeight'],
                'underWidth'  => 'ширина загруженного изображения меньше ' . Yii::$app->params['thumbnailDefaultWidth'] . ' пикселей, загрузите изображение крупнее',
                'underHeight' => 'высота загруженного изображения должна быть не меньше ' . Yii::$app->params['thumbnailDefaultHeight'] . ' пикселей, загрузите изображение крупнее'
            ],
            [
                'date',
                'required',
                'message' => 'дата выхода должна быть заполнена'
            ],
            [
                'date',
                'default',
                'value' => function ($model, $attribute) {
                    return date('Y-m-d');
                }
            ],
            [
                'date',
                'filter',
                'filter' => function ($value) {
                    if (
                        preg_match('/(\d{2})\.(\d{2})\.(\d{4})/u', $value, $matches) &&
                        isset($matches[1], $matches[2], $matches[3])
                    ) {
                        return $matches[3] . '-' . $matches[2] . '-' . $matches[1];
                    }
                    return $value;
                }
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'name'        => 'Название',
            'date_create' => 'Дата добавления',
            'date_update' => 'Дата обновления',
            'preview'     => 'Превью',
            'date'        => 'Дата выхода книги',
            'author_id'   => 'Автор',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(Author::className(), ['id' => 'author_id']);
    }

    /**
     * @inheritdoc
     * @return BookQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new BookQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            // удаляем изображение модели, если оно имеется
            if ($this->preview != '') {
                $filename = realpath(
                    Yii::$app->params['frontendWebrootDir'] . $this->preview
                );
                $thumb = realpath(
                    preg_replace('~^(.*?)(\d+)\.(jpe?g|png|gif)$~iu', '$1$2_thumb.$3', Yii::$app->params['frontendWebrootDir'] . $this->preview)
                );
                if (false !== $filename) {
                    unlink($filename);
                }
                if (false !== $thumb) {
                    unlink($thumb);
                }
            }
            return true;
        } else {
            return false;
        }
    }
}
