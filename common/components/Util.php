<?php

namespace common\components;

use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\base\InvalidParamException;

/**
 * Class Util
 *
 * @package common\components
 */
final class Util extends Component
{
    /**
     * Создает вложенные директории, начиная с заданной директории $dir,
     * на основе переданного вторым параметром десятичного числа $integer.
     * Последнее число преобразуется в шестнадцатеричный формат,
     * при необходимости дополняется до кратного $chunk размера строки и,
     * на основе этой строки создается иерархическая структура вложенных
     * директорий.
     *
     * @param string  $dir
     * @param integer $integer
     * @param integer $chunk
     *
     * @return string созданный абсолютный путь
     * @throws \yii\base\Exception
     */
    public static function createNestedFolders($dir, $integer, $chunk = 2)
    {
        $dir = realpath($dir);

        if (!is_dir($dir)) {
            throw new InvalidParamException("Directory $dir is not a valid directory.");
        }
        if (!is_writeable($dir)) {
            throw new Exception("Directory $dir is not writable.");
        }

        $integer = abs(intval($integer));
        $hex     = dechex($integer);

        if (0 != ($remainder = strlen($hex) % $chunk)) {
            $hex = str_repeat('0', $chunk - $remainder) . $hex;
        }

        $array = str_split($hex, $chunk);
        $path  = $dir . DIRECTORY_SEPARATOR .
                 (count($array) > 1 ?
                     implode(DIRECTORY_SEPARATOR, $array) :
                     array_shift($array));

        if (!is_dir($path)) {
            if (false === mkdir($path, 0777, true)) {
                throw new Exception("Невозможно создать директорию $path");
            }
        }

        return $path;
    }

    /**
     * Позволяет получить абсолютный путь всех вложенных директорий,
     * начиная с заданной директории $dir,
     * на основе переданного вторым параметром десятичного числа $integer.
     * Последнее число преобразуется в шестнадцатеричный формат,
     * при необходимости дополняется до кратного $chunk размера.
     *
     * @param string  $dir
     * @param integer $integer
     * @param integer $chunk
     *
     * @return string полученный абсолютный путь
     * @throws \yii\base\Exception
     */
    public static function getNestedFolders($dir, $integer, $chunk = 2)
    {
        $dir = realpath($dir);

        if (!is_dir($dir)) {
            throw new InvalidParamException("Directory $dir is not a valid directory.");
        }

        $integer = abs(intval($integer));
        $hex     = dechex($integer);

        if (0 != ($remainder = strlen($hex) % $chunk)) {
            $hex = str_repeat('0', $chunk - $remainder) . $hex;
        }

        $array = str_split($hex, $chunk);
        $path  = $dir . DIRECTORY_SEPARATOR .
                 (count($array) > 1 ?
                     implode(DIRECTORY_SEPARATOR, $array) :
                     array_shift($array));

        return $path;
    }

    /**
     * Транслитерация русской кириллицы в латиницу.
     *
     * @param string $string        транслитерируемая строка в кириллице
     * @param bool   $replaceSpace  заменять ли пробельные символы в
     *                              транслитерируемой строке?
     * @param string $spaceReplacer на что заменять пробельные символы
     *
     * @return string
     */
    public static function translit($string, $replaceSpace = true, $spaceReplacer = '-')
    {
        // force using UTF-8 encoding
        $string = trim(mb_convert_encoding($string, 'UTF-8', mb_detect_encoding($string)));

        $patterns=array(
            '/А/', '/Б/', '/В/', '/Г/', '/Д/', '/Е/', '/Ё/', '/Ж/', '/З/', '/И/', '/Й/',
            '/К/', '/Л/', '/М/', '/Н/', '/О/', '/П/', '/Р/', '/С/', '/Т/', '/У/', '/Ф/',
            '/Х/', '/Ц/', '/Ч/', '/Ш/', '/Щ/', '/Ъ/', '/Ы/', '/Ь/', '/Э/', '/Ю/', '/Я/',
            '/а/', '/б/', '/в/', '/г/', '/д/', '/е/', '/ё/', '/ж/', '/з/', '/и/', '/й/',
            '/к/', '/л/', '/м/', '/н/', '/о/', '/п/', '/р/', '/с/', '/т/', '/у/', '/ф/',
            '/х/', '/ц/', '/ч/', '/ш/', '/щ/', '/ъ/', '/ы/', '/ь/', '/э/', '/ю/', '/я/'
        );
        // use 'u' modifier in all patterns
        array_walk($patterns, function (&$v) {
            return $v .= 'u';
        });

        $replacements=array(
            'A',    'B',   'V',   'G',   'D',   'E',   'Yo',  'Zh',  'Z',   'I',   'J',
            'K',    'L',   'M',   'N',   'O',   'P',   'R',   'S',   'T',   'U',   'F',
            'H',    'Ts',  'Ch',  'Sh',  'Sch', '',    'Y',   '',    'E',   'Yu',  'Ya',
            'a',    'b',   'v',   'g',   'd',   'e',   'yo',  'zh',  'z',   'i',   'j',
            'k',    'l',   'm',   'n',   'o',   'p',   'r',   's',   't',   'u',   'f',
            'h',    'ts',  'ch',  'sh',  'sch', '',    'y',   '',    'e',   'yu',  'ya'
        );

        $string = preg_replace($patterns, $replacements, $string);

        // убираем все "лишние" символы
        $string = preg_replace('/[^a-z0-9_ -]+/iu', '', $string);

        // space replacing
        if ($replaceSpace) {
            $string = preg_replace('/[\s]{2,}/', ' ', $string);
            $string = preg_replace('/ /', $spaceReplacer, $string);
        }

        // реплейсер должен встречаться только один раз подряд!
        $string = preg_replace('/'.$spaceReplacer.'{2,}/iu', $spaceReplacer, $string);

        return $string;
    }


    /**
     * Метод возвращает окончание для множественного числа слова на основании
     * числа и массива окончаний
     *
     * @param  integer $number       Число на основе которого нужно
     *                               сформировать окончание
     * @param  array   $endingArray  Массив слов или окончаний для чисел
     *                               (1, 4, 5), например:
     *                               array('яблоко', 'яблока', 'яблок')
     *
     * @return string
     * @link http://habrahabr.ru/post/105428/
     */
    public static function getNumEnding($number, $endingArray)
    {
        $number = $number % 100;
        if ($number >= 11 && $number <= 19) {
            $ending = $endingArray[2];
        } else {
            $i = $number % 10;
            switch ($i) {
                case (1):
                    $ending = $endingArray[0];
                    break;
                case (2):
                case (3):
                case (4):
                    $ending = $endingArray[1];
                    break;
                default:
                    $ending = $endingArray[2];
            }
        }

        return $ending;
    }
}