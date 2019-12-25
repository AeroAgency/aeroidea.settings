<?php
/**
 * Created by PhpStorm.
 * User: aero
 * Date: 23.12.19
 * Time: 16:42
 */

namespace Aeroidea\Settings\Migrations\Helpers;

use Aeroidea\Settings\Options\OptionTable;
use Aeroidea\Settings\Options\ValuesTable;
use Aeroidea\Settings\Options\ConfigurationTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Sprint\Migration\Helper;

class ConfigurationHelper extends Helper
{
    /**
     * Получает конфигурацию, бросает исключение если его не существует
     * @param $id
     * @throws \Sprint\Migration\Exceptions\HelperException
     * @return array|void
     */
    public function getConfigurationByIdIfExists($id)
    {
        $config = $this->getConfigurationById($id);
        if ($config && isset($config['ID'])) {
            return $config;
        }
        $this->throwException(__METHOD__, 'configuration not found');
    }

    /**
     * Получает конфигурацию
     * @param $id
     * @return array
     */
    public function getConfigurationById($id)
    {
        $config = [];
        /** @compatibility filter or $id */
        $filter = is_array($id) ? $id : [
            '=ID' => $id,
        ];

        try {
            $config = ConfigurationTable::getList(["filter" => $filter])->fetch();
        } catch (ObjectPropertyException | ArgumentException | SystemException $e) {
        }

        return $config;
    }

    /**
     * Получает конфигурацию, бросает исключение если его не существует
     * @param $code
     * @throws \Sprint\Migration\Exceptions\HelperException
     * @return array|void
     */
    public function getConfigurationByCodeIfExists($code)
    {
        $config = $this->getConfigurationByCode($code);
        if ($config && isset($config['ID'])) {
            return $config;
        }
        $this->throwException(__METHOD__, 'configuration not found');
    }

    /**
     * Получает конфигурацию
     * @param $id
     * @return array
     */
    public function getConfigurationByCode($code)
    {
        $config = [];
        /** @compatibility filter or $code */
        $filter = is_array($code) ? $code : [
            '=CODE' => $code,
        ];

        try {
            $config = ConfigurationTable::getList(["filter" => $filter])->fetch();
        } catch (ObjectPropertyException | ArgumentException | SystemException $e) {
        }
        return $config;
    }

    /**
     * Получает настройки конфигурации
     * @param $optionsId
     * @return array
     */
    public function getOptions($optionsId)
    {
        $options = [];
        $filter = [
            'ID' => $optionsId,
        ];
        try {
            $options = OptionTable::getList([
                "filter" => $filter])->fetchAll();
        } catch (ObjectPropertyException | ArgumentException | SystemException $e) {
        }

        return $options;
    }

    /**
     * Получает настройки конфигурации со значениями
     * @param $optionsId
     * @return array
     */
    public function getOptionsWithValue($optionsId)
    {
        $options = [];
        $filter = [
            'ID' => $optionsId,
        ];
        $valueFilter = [
            "OPTION_ID" => $optionsId
        ];
        try {
            $options = OptionTable::getList([
                "filter" => $filter])->fetchAll();
            $rsValues = ValuesTable::getList([
                "filter" => $valueFilter])->fetchAll();
            $values = [];
            foreach ($rsValues as $value) {
                $values[$value["OPTION_ID"]] = $value["VALUE"];
            }

            foreach ($options as &$option) {
                $option["VALUE"] = $values[$option["ID"]];
            }
            unset($option);
        } catch (ObjectPropertyException | ArgumentException | SystemException $e) {
        }

        return $options;
    }

    /**
     * Получает настройки конфигурации
     * @param $code
     * @return array|false
     */
    public function getOption($configId, $code)
    {
        $filter = [
            'CODE' => $code,
            'CONFIGURATION_ID' => $configId
        ];
        try {
            $option = OptionTable::getList([
                "filter" => $filter])->fetch();
        } catch (ObjectPropertyException | ArgumentException | SystemException $e) {
        }
        return $option;
    }

    /**
     * Получает значение настройки конфигурации
     * @param $optionsId
     * @return array|false
     */
    public function getValue($optionsId)
    {
        $valueFilter = [
            "OPTION_ID" => $optionsId
        ];
        try {
            $value = ValuesTable::getList([
                "filter" => $valueFilter])->fetch();
        } catch (ObjectPropertyException | ArgumentException | SystemException $e) {
        }
        return $value;
    }

    /**
     * Получает конфигурацию
     * Данные подготовлены для экспорта в миграцию или схему
     * @param $configId
     * @return mixed
     */
    public function exportConfiguration($configId)
    {
        return $this->prepareExportConfiguration(
            $this->getConfigurationById($configId)
        );
    }

    protected function prepareExportConfiguration($item)
    {
        if (empty($item)) {
            return $item;
        }

        unset($item['ID']);
        return $item;
    }

    protected function prepareExportOption($item)
    {
        if (empty($item)) {
            return $item;
        }

        unset($item['ID']);
        unset($item['CONFIGURATION_ID']);
        return $item;
    }

    protected function prepareExportOptions($items)
    {
        foreach ($items as &$item) {
            $this->prepareExportOption($item);
        }
        unset($item);
        return $items;
    }

    /**
     * Получает настройки
     * Данные подготовлены для экспорта в миграцию или схему
     * @param $optionsId
     * @return mixed
     */
    public function exportOptions($optionsId)
    {
        return $this->prepareExportOptions(
            $this->getOptionsWithValue($optionsId)
        );
    }

    /**
     * Сохраняет конфигурацию
     * Создаст если не было, обновит если существует и отличается
     * @param array $fields
     * @return bool
     * @throws \Sprint\Migration\Exceptions\HelperException
     */
    public function saveConfiguration($fields = [])
    {
        $this->checkRequiredKeys(__METHOD__, $fields, ['CODE', 'TITLE']);
        $config = $this->getConfigurationByCode($fields["CODE"]);
        $exists = $this->prepareExportConfiguration($config);
        if (empty($config)) {
            $ok = $this->getMode('test') ? true : $this->addConfiguration($fields);
            $this->outNoticeIf($ok, 'Конфигугация "%s" [%s]: добавлена', $fields['TITLE'], $fields['CODE']);
            return $ok;
        }
        if ($this->hasDiff($exists, $fields)) {
            $ok = $this->getMode('test') ? true : $this->updateConfiguration($config['ID'], $fields);
            $this->outNoticeIf($ok, 'Конфигугация "%s" [%s]: обновлена', $fields['TITLE'], $fields['CODE']);
            $this->outDiffIf($ok, $exists, $fields);
            return $ok;
        }
        $ok = $this->getMode('test') ? true : $config['ID'];
        if ($this->getMode('out_equal')) {
            $this->outIf($ok, 'Конфигугация %s: совпадает', $fields['CODE']);
        }
        return $ok;
    }

    /**
     * Добавление конфигурации
     * @param $fields
     * @return int|void
     * @throws \Sprint\Migration\Exceptions\HelperException
     */
    public function addConfiguration($fields)
    {
        try {
            $result = ConfigurationTable::add($fields);
            if ($result->isSuccess()) {
                return $result->getId();
            }
        } catch (\Exception $e) {
            $this->throwException(__METHOD__, $e->getMessage());
        }
    }

    /**
     * Обновляет конфигурацию
     * @param $configId
     * @param $fields
     * @return int|void
     * @throws \Sprint\Migration\Exceptions\HelperException
     */
    public function updateConfiguration($configId, $fields)
    {
        try {
            $result = ConfigurationTable::update($configId, $fields);
            if ($result->isSuccess()) {
                return $configId;
            }
        } catch (\Exception $e) {
            $this->throwException(__METHOD__, $e->getMessage());
        }
    }

    /**
     * Сохраняет настройку конфигурации со значением
     * Создаст если не было, обновит если существует и отличается
     * @param $configId
     * @param $fields
     * @throws \Sprint\Migration\Exceptions\HelperException
     */
    public function saveOptionWithValue($configId, $fields)
    {
        $value = "";
        if (!empty($fields["VALUE"])) {
            $value = $fields["VALUE"];
            unset($fields["VALUE"]);
        }
        $optionId = $this->saveOption($configId, $fields);
        $this->saveValue($optionId, $value);
    }

    /**
     * Сохраняет настройку конфигурации
     * Создаст если не было, обновит если существует и отличается
     * @param $configId
     * @param $fields
     * @return bool|mixed
     * @throws \Sprint\Migration\Exceptions\HelperException
     */
    public function saveOption($configId, $fields)
    {
        $this->checkRequiredKeys(__METHOD__, $fields, ['CODE', 'TITLE']);
        $option = $this->getOption($configId, $fields["CODE"]);
        $exists = $this->prepareExportOption($option);
        if (empty($option['ID'])) {
            $ok = $this->getMode('test') ? true : $this->addOption($configId, $fields);
            \Aero\Main\Util::log([$ok, "add", $fields], "log-saveOption.txt");
            $this->outNoticeIf($ok, 'Настройка конфигурации "%s" [%s]: добавлена', $fields['TITLE'], $fields['CODE']);
            return $ok;
        }

        if ($this->hasDiff($exists, $fields)) {
            $ok = $this->getMode('test') ? true : $this->updateOption($option['ID'], $fields);
            $this->outNoticeIf($ok, 'Настройка конфигурации "%s" [%s]: обновлена', $fields['TITLE'], $fields['CODE']);
            $this->outDiffIf($ok, $exists, $fields);
            return $ok;
        }
        $ok = $this->getMode('test') ? true : $option['ID'];
        if ($this->getMode('out_equal')) {
            $this->outIf($ok, 'Настройка конфигурации %s: совпадает', $fields['CODE']);
        }
        return $ok;
    }

    /**
     * Добавляет настройку конфигурации
     * @param $fields
     * @return int|void
     * @throws \Sprint\Migration\Exceptions\HelperException
     */
    public function addOption($configId, $fields)
    {
        try {
            $fields['CONFIGURATION_ID'] = $configId;
            $result = OptionTable::add($fields);
            if ($result->isSuccess()) {
                return $result->getId();
            }
        } catch (\Exception $e) {
            $this->throwException(__METHOD__, $e->getMessage());
        }
    }

    /**
     * Обновляет настройку конфигурации
     * @param  $optionId
     * @param $fields
     * @return int|void
     * @throws \Sprint\Migration\Exceptions\HelperException
     */
    public function updateOption($optionId, $fields)
    {
        try {
            $result = OptionTable::update($optionId, $fields);
            if ($result->isSuccess()) {
                return $optionId;
            }
        } catch (\Exception $e) {
            $this->throwException(__METHOD__, $e->getMessage());
        }
    }

    /**
     * Сохраняет значение настройки конфигурации
     * Создаст если не было, обновит если существует и отличается
     * @param $optionId
     * @param $value
     * @return bool|mixed
     * @throws \Sprint\Migration\Exceptions\HelperException
     */
    public function saveValue($optionId, $value)
    {
        if (!empty($optionId)) {
            $optionValue = $this->getValue($optionId);

            if (empty($optionValue)) {
                $ok = $this->getMode('test') ? true : $this->addValue($optionId, $value);
                $this->outNoticeIf($ok, 'Значение настройки конфигурации: добавлено');
                return $ok;
            }

            if (!empty($optionValue) && ($optionValue["VALUE"] != $value)) {
                $ok = $this->getMode('test') ? true : $this->updateValue($optionValue['ID'], $optionId, $value);
                $this->outNoticeIf($ok, 'Значение настройки конфигурации: обновлено');
                return $ok;
            }

            $ok = $this->getMode('test') ? true : $optionValue['ID'];
            if ($this->getMode('out_equal')) {
                $this->outIf($ok, 'Значение настройки конфигурации: совпадает');
            }
            return $ok;
        }
    }

    /**
     * Добавляет значение настройки конфигурации
     * @param $optionId
     * @param $value
     * @return int|void
     * @throws \Sprint\Migration\Exceptions\HelperException
     */
    public function addValue($optionId, $value)
    {
        try {
            if (!empty($optionId)) {
                $result = ValuesTable::add([
                    'OPTION_ID' => $optionId,
                    'VALUE' => $value
                ]);
                if ($result->isSuccess()) {
                    return $result->getId();
                }
            }
        } catch (\Exception $e) {
            $this->throwException(__METHOD__, $e->getMessage());
        }
        $this->throwException(__METHOD__, " no found option Id");
    }

    /**
     * Обнавляет значение настройки конфигурации
     * @param $id
     * @param $optionId
     * @param $value
     * @return array|int|string
     * @throws \Sprint\Migration\Exceptions\HelperException
     */
    public function updateValue($id, $optionId, $value)
    {
        try {
            if (!empty($optionId) && !empty($id)) {
                $result = ValuesTable::update($id, [
                    'OPTION_ID' => $optionId,
                    'VALUE' => $value
                ]);
                if ($result->isSuccess()) {
                    return $result->getId();
                }
            }
        } catch (\Exception $e) {
            $this->throwException(__METHOD__, $e->getMessage());
        }
        $this->throwException(__METHOD__, " no found option Id");
    }

    /**
     * Удаляет настройку концигурации если она существует
     * @param $configId
     * @param $optionCode
     * @throws \Sprint\Migration\Exceptions\HelperException
     * @return bool|void
     * @throws \Exception
     */
    public function deleteOptionIfExists($configId, $optionCode)
    {
        $option = $this->getOption($configId, $optionCode);
        if (!$option) {
            return false;
        }
        return $this->deleteOption($option["ID"]);
    }

    /**
     * Удаляет настройку концигурации
     * @param $optionId
     * @return bool
     * @throws \Exception
     * @throws \Sprint\Migration\Exceptions\HelperException
     */
    public function deleteOption($optionId)
    {
        if (OptionTable::delete($optionId)->isSuccess()) {
            return true;
        }
        $this->throwException(__METHOD__, 'Could not delete configuration option %s', $optionId);
    }

    /**
     * Удаляет концигурацию если она существует
     * @param $code
     * @return bool
     * @throws \Exception
     * @throws \Sprint\Migration\Exceptions\HelperException
     */
    public function deleteConfigurationIfExists($code)
    {
        $configuration = $this->getConfigurationByCode($code);
        if (!$configuration) {
            return false;
        }
        return $this->deleteConfiguration($configuration["ID"]);
    }

    /**
     * Удаляет концигурацию
     * @param $configurationId
     * @return bool
     * @throws \Exception
     * @throws \Sprint\Migration\Exceptions\HelperException
     */
    public function deleteConfiguration($configurationId)
    {
        if (ConfigurationTable::delete($configurationId)->isSuccess()) {
            return true;
        }
        $this->throwException(__METHOD__, 'Could not delete configuration %s', $configurationId);
    }
}