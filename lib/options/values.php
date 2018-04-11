<?php
namespace Aero\Settings\Options;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\Validator\Unique;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;


Loc::loadMessages(__FILE__);
/**
 * Модель значений полей настроек.
 */
class ValuesTable extends DataManager
{
    /**
     * @inheritdoc
     */
    public static function getTableName()
    {
        return 'd_ah_option_value';
    }

    /**
     * @inheritdoc
     */
    public static function getMap()
    {
        return [
            'ID' => array(
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true
            ),
            'OPTION_ID' => [
                'data_type' => 'integer',
                'title' => Loc::getMessage("OPTION_ID"),
                'required' => true,
            ],
            'VALUE' => [
                'data_type' => 'string',
                'title' => Loc::getMessage("VALUE"),
            ]
        ];
    }

    public static function getFilePath()
    {
        return __FILE__;
    }

    /**
     * Возвращает значения настроек конфигурации
     * @param $configurationId
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     */
    public static function getConfigurationOptionsValues($configurationId)
    {
        $rs = OptionTable::getList(
            [
                'select' => ['ID', 'TITLE', 'TYPE', 'MULTIPLE', 'CODE'],
                'filter' => [
                    'CONFIGURATION_ID' => $configurationId
                ]
            ]
        );
        $optionsId = [];
        $arData = [];
        while ($ar = $rs->fetch()) {
            $optionsId[] = $ar['ID'];
            $arData[$ar['ID']]['ID'] = $ar['ID'];
            $arData[$ar['ID']]['TITLE'] = $ar['TITLE'];
            $arData[$ar['ID']]['TYPE'] = $ar['TYPE'];
            $arData[$ar['ID']]['MULTIPLE'] = $ar['MULTIPLE'];
            $arData[$ar['ID']]['CODE'] = $ar['CODE'];
            $arData[$ar['ID']]['VALUE'] = '';
            $arData[$ar['ID']]['VALUE_ID'] = '';
        }
        if(!empty($optionsId)) {
            $rs = self::getList(
                [
                    'select' => ['OPTION_ID', 'VALUE', 'ID'],
                    'filter' => [
                        'OPTION_ID' => $optionsId
                    ]
                ]
            );

            while ($ar = $rs->fetch()) {
                $arData[$ar['OPTION_ID']]['VALUE'] = $ar['VALUE'];
                $arData[$ar['OPTION_ID']]['VALUE_ID'] = $ar['ID'];
            }
        }
        $arData = array_values($arData);
        return $arData;
    }
}