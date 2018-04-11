<?php
namespace Aero\Settings\Options\AdminInterface;

use Aero\Settings\Options\ValuesTable;
use Aero\Settings\Options\Widget\EmptyDateTimeWidget;
use Aero\Settings\Options\Widget\StringCheckboxWidget;
use Aero\Settings\Util;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Localization\Loc;
use DigitalWand\AdminHelper\Helper\AdminBaseHelper;
use DigitalWand\AdminHelper\Helper\AdminInterface;
use DigitalWand\AdminHelper\Widget\CheckboxWidget;
use DigitalWand\AdminHelper\Widget\ComboBoxWidget;
use DigitalWand\AdminHelper\Widget\DateTimeWidget;
use DigitalWand\AdminHelper\Widget\FileWidget;
use DigitalWand\AdminHelper\Widget\HLIBlockFieldWidget;
use DigitalWand\AdminHelper\Widget\IblockElementWidget;
use DigitalWand\AdminHelper\Widget\NumberWidget;
use DigitalWand\AdminHelper\Widget\StringWidget;
use Bitrix\Main\Application;
use DigitalWand\AdminHelper\Widget\VisualEditorWidget;


Loc::loadMessages(__FILE__);
/**
 * Описание интерфейса (табок и полей) админки конфигурации настроек.
 *
 * {@inheritdoc}
 */
class ValuesAdminInterface extends AdminInterface
{
    /**
     * @var string id конфигурации
     */
    protected $configId;

    /**
     * @var array Дополнительные данные по конфигурации
     */
    protected $configData;

    /**
     * ValuesAdminInterface constructor.
     * @throws ArgumentException
     */
    public function __construct()
    {
        $request = Application::getInstance()->getContext()->getRequest();
        $id = htmlspecialchars($request->getQuery("ID"));
        $this->configId = $id;

        $arrAddData = ValuesTable::getConfigurationOptionsValues($this->configId);
        $this->configData = $arrAddData;
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        return [
            'OPTIONS' => [
                'NAME' => Loc::getMessage("SETTINGS"),
                'FIELDS' => $this->getOptionFields()
            ],
            'MAIN' => [
                'NAME' => Loc::getMessage("CONFIGURATION"),
                'FIELDS' => [
                    'ID' => array(
                        'WIDGET' => new NumberWidget(),
                        'READONLY' => true,
                        'FILTER' => true,
                        'HIDE_WHEN_CREATE' => true
                    ),
                    'TITLE' => array(
                        'WIDGET' => new StringWidget(),
                        'SIZE' => '80',
                        'FILTER' => '%',
                        'READONLY' => true
                    ),
                    'CODE' => array(
                        'WIDGET' => new StringWidget(),
                        'SIZE' => '80',
                        'FILTER' => '%',
                        'READONLY' => true
                    ),
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function helpers()
    {
        return [
            ValuesListHelper::class => [
                'BUTTONS' => false
            ],
            ValuesEditHelper::class => [
                'BUTTONS' => false
            ]
        ];
    }

    /**
     * Регистрируем поля, табы и кнопки.
     * @throws ArgumentException
     */
    public function registerData()
    {
        $fieldsAndTabs = array('FIELDS' => array(), 'TABS' => array());
        $tabsWithFields = $this->fields();

        $arrConfigData = Util::getIndexedArray(
            $this->configData,
            'CODE'
        );

        // приводим массив хелперов к формату класс => настройки
        $helpers = array();

        foreach ($this->helpers() as $key => $value) {
            if (is_array($value)) {
                $helpers[$key] = $value;
            }
            else {
                $helpers[$value] = array();
            }
        }

        $helperClasses = array_keys($helpers);
        /**
         * @var \Bitrix\Main\Entity\DataManager
         */
        $model = $helperClasses[0]::getModel();
        foreach ($tabsWithFields as $tabCode => $tab) {
            $fieldsAndTabs['TABS'][$tabCode] = $tab['NAME'];

            foreach ($tab['FIELDS'] as $fieldCode => $field) {
                if (empty($field['TITLE']) && $model) {
                    try {
                        $field['TITLE'] = $model::getEntity()->getField($fieldCode)->getTitle();
                    } catch (ArgumentException $e) {
                        if($arrConfigData[$fieldCode]) {
                            $field['TITLE'] = $arrConfigData[$fieldCode]['TITLE'];
                        } else {
                            throw new ArgumentException($e->getMessage());
                        }
                    }

                }

                $field['TAB'] = $tabCode;
                $fieldsAndTabs['FIELDS'][$fieldCode] = $field;
            }
        }

        AdminBaseHelper::setInterfaceSettings($fieldsAndTabs, $helpers, $helperClasses[0]::getModule());

        foreach ($helperClasses as $helperClass) {
            /**
             * @var AdminBaseHelper $helperClass
             */
            $helperClass::setInterfaceClass(get_called_class());
        }
    }

    protected function getOptionFields()
    {
        $arFields = [];
        foreach ($this->configData as $arConfigElement) {
            switch ($arConfigElement['TYPE']) {
                case 'text': {
                    $arFields[$arConfigElement['CODE']] = [
                        'WIDGET' => new VisualEditorWidget(),
                        'HEADER' => false
                    ];
                    break;
                }
                case 'file': {
                    $arFields[$arConfigElement['CODE']] = [
                        'WIDGET' => new FileWidget(),
                        'HEADER' => false
                    ];
                    break;
                }
                case 'checkbox': {
                    $arFields[$arConfigElement['CODE']] = [
                        'WIDGET' => new StringCheckboxWidget(),
                        'VIRTUAL' => true
                    ];
                    break;
                }
                case 'datetime': {
                    $arFields[$arConfigElement['CODE']] = [
                        'WIDGET' => new EmptyDateTimeWidget(),
                        'DEFAULT' => '',
                        'VIRTUAL' => true
                    ];
                    break;
                }
                case 'iblock_element': {
                    $arFields[$arConfigElement['CODE']] = [
                        'WIDGET' => new IblockElementWidget(),
                        'VIRTUAL' => true
                    ];
                    break;
                }
                default: {
                    $arFields[$arConfigElement['CODE']] = [
                        'WIDGET' => new StringWidget(),
                        'SIZE' => '80',
                        'FILTER' => '%',
                    ];
                    break;
                }
            }


        }

        return $arFields;
    }
}