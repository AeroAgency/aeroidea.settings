<?php

/**
 * Aero settings module
 *
 * @category    aero
 * @link        http://aeroidea.ru
 */

namespace Aeroidea\Settings;

use Aeroidea\Settings\Options\ConfigurationTable;
use Aeroidea\Settings\Options\OptionTable;
use Aeroidea\Settings\Options\ValuesTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity\DataManager;

/**
 * Работа с настройками
 *
 * @category    aero
 */
class Config
{
    /**
     * Тег кеширования
     *
     * @var string
     */
    protected static $optionCacheTag = 'aero_settings_options';

    /**
     * @var null
     */
    protected static $memoryCacheOptions = null;

    /**
     * Получение значения опции конфигурации
     *
     * @param string $config код конфигурации
     * @param string $option код настройки
     * @param int $cacheTime
     * @return mixed
     * @throws ArgumentException
     * @throws \Exception
     */
    public static function get($config, $option, $cacheTime = 3600)
    {
        $registryValue = self::getRegistry($config, $option);
        if($registryValue !== null) {
            return $registryValue;
        }
        $cache = new Cache(
            md5($config . '_' . $option),
            self::class,
            $cacheTime,
            self::$optionCacheTag
        );
        if ($cache->start()) {
            $optionId = self::getOptionId($config, $option);
            if (!$optionId) {
                throw new \Exception('Не найдена настройка конфигурации ' . $config . ' с кодом ' . $option);
            }
            $data = ValuesTable::getList(
                [
                    'filter' => ['OPTION_ID' => $optionId],
                    'select' => ['VALUE']
                ]
            )->fetch();
            $value = $data['VALUE'];

            if ($value) {
                $cache->end($value);
                self::setRegistry($config, $option, $value);
            } else {
                $cache->abort();
            }
        } else {
            $value = $cache->getVars();
        }
        return $value;

    }

    /**
     * Сохраняет значение опции конфигурации
     *
     * @param string $config код конфигурации
     * @param string $option код настройки
     * @param mixed $value Значение, которое предполагаем установить
     * @throws ArgumentException
     * @throws \Exception
     */
    public static function set($config, $option, $value)
    {
        $optionId = self::getOptionId($config, $option);
        if (!$optionId) {
            throw new \Exception('Не найдена настройка конфигурации ' . $config . ' с кодом ' . $option);
        }
        $data = ValuesTable::getList(
            [
                'filter' => ['OPTION_ID' => $optionId],
                'select' => ['ID']
            ]
        )->fetch();
        $valueId = $data['ID'];
        if (!$valueId) {
            throw new \Exception('Не найдено значение настройки конфигурации ' . $config . ' с кодом ' . $option);
        }
        ValuesTable::update($valueId, ['VALUE' => $value]);
        self::setRegistry($config, $option, $value);
        self::clearCache();
    }

    /**
     * Возвращает id опции по коду конфига и коду опции
     *
     * @param $config
     * @param $option
     * @return mixed
     * @throws ArgumentException
     * @throws \Exception
     */
    protected static function getOptionId($config, $option)
    {
        $configId = self::getIdByCode(ConfigurationTable::class, $config);
        $data = OptionTable::getList(
            [
                'filter' => ['CODE' => $option, 'CONFIGURATION_ID' => $configId],
                'select' => ['ID', 'CODE']
            ]
        )->fetch();
        $optionId = $data['ID'];
        return $optionId;
    }

    /**
     * Возвращает id записи сущности по коду
     *
     * @param string $className имя класса
     * @param string $code код записи
     * @return int
     * @throws ArgumentException
     * @throws \Exception
     */
    protected static function getIdByCode($className, $code)
    {
        /**
         * @var DataManager $className
         */
        $data = $className::getList(
            [
                'filter' => ['CODE' => $code],
                'select' => ['ID', 'CODE']
            ]
        )->fetch();
        if ($code != $data['CODE']) {
            throw new \Exception('Не найдена запись с кодом ' . $code);
        }
        $id = $data['ID'];
        return $id;
    }

    /**
     * Возвращает значение опции конфигурации из реестра
     *
     * @param $config
     * @param $option
     * @return null
     */
    public static function getRegistry($config, $option)
    {
        $code = $config . '_' . $option;
        return array_key_exists($code, self::$memoryCacheOptions) ? self::$memoryCacheOptions[$code] : null;
    }

    /**
     * Сохраняет значение опции конфигурации в реестр
     * @param $config
     * @param $option
     * @param $value
     */
    public static function setRegistry($config, $option, $value)
    {
        $code = $config . '_' . $option;
        self::$memoryCacheOptions[$code] = $value;
    }

    /**
     * Сбрасывает тег кеширования
     */
    public static function clearCache()
    {
        global $CACHE_MANAGER;
        $CACHE_MANAGER->ClearByTag(self::$optionCacheTag);
    }
}