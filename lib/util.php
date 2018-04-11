<?php

/**
 * Aero settings module
 *
 * @category    aero
 * @link        http://aeroidea.ru
 */

namespace Aero\Settings;


/**
 * Утилиты
 *
 * @category    aero
 */
class Util
{
    /**
     * Возвращает массив значений указанного ключа исходного массива
     * Например, нужно, чтобы получать из мссива array(array("ID" => 1), array("ID" => 2), array("ID" => 3))
     * массив array(1, 2, 3)
     *
     *
     * @param array $arr
     * @param string $key
     * @param bool $notNull
     * @return array
     */

    public static function getAssocArrItemsKey($arr, $key = "ID", $notNull = false)
    {
        $resArr = array();
        foreach ($arr as $item) {
            if ($notNull && !$item[$key]) {
                continue;
            }
            $resArr[] = $item[$key];
        }
        return $resArr;
    }

    /**
     * Индексирует массив по заданному ключу
     * @param $arr
     * @param string $key
     *
     * @return array
     */
    public static function getIndexedArray($arr, $key = "ID")
    {

        $arRes = array();
        foreach ($arr as $index => $arrItem) {
            $arrItem['INDEX'] = $index;
            $arRes[$arrItem[$key]] = $arrItem;
        }

        return $arRes;
    }
}