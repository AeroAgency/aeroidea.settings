<?php

namespace Aeroidea\Settings\Options\AdminInterface;

use Bitrix\Main\Localization\Loc;
use DigitalWand\AdminHelper\Helper\AdminInterface;
use DigitalWand\AdminHelper\Widget\CheckboxWidget;
use DigitalWand\AdminHelper\Widget\ComboBoxWidget;
use DigitalWand\AdminHelper\Widget\NumberWidget;
use DigitalWand\AdminHelper\Widget\OrmElementWidget;
use DigitalWand\AdminHelper\Widget\StringWidget;

Loc::loadMessages(__FILE__);

/**
 * Описание интерфейса (табок и полей) админки конфигурации настроек.
 *
 * {@inheritdoc}
 */
class OptionAdminInterface extends AdminInterface
{
    /**
     * @inheritdoc
     */
    public function dependencies()
    {
        return [ConfigurationAdminInterface::class];
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        return [
            'MAIN' => [
                'NAME' => Loc::getMessage("SETTING"),
                'FIELDS' => [
                    'ID' => [
                        'WIDGET' => new NumberWidget(),
                        'READONLY' => true,
                        'FILTER' => true,
                        'HIDE_WHEN_CREATE' => true,
                        'EDIT_IN_LIST' => false
                    ],
                    'CODE' => [
                        'WIDGET' => new StringWidget(),
                        'SIZE' => 80,
                        'FILTER' => '%',
                        'REQUIRED' => true,
                        'EDIT_IN_LIST' => false
                    ],
                    'TYPE' => [
                        'WIDGET' => new ComboBoxWidget(),
                        'REQUIRED' => true,
                        'VARIANTS' => [
                            'string' => 'Строка',
                            'text' => 'Текст',
                            'checkbox' => 'Флаг',
                            'file' => 'Файл',
                            'datetime' => 'Дата со временем',
                            'iblock_element' => 'Привязка к элементу инфоблока'
                        ],
                        'DEFAULT_VARIANT' => 'string',
                        'EDIT_IN_LIST' => false
                    ],
                    'TITLE' => [
                        'WIDGET' => new StringWidget(),
                        'SIZE' => 80,
                        'FILTER' => '%',
                        'REQUIRED' => true,
                        'EDIT_IN_LIST' => false
                    ],
                    'CONFIGURATION_ID' => [
                        'WIDGET' => new OrmElementWidget(),
                        'FILTER' => true,
                        'HEADER' => false,
                        'HELPER' => ConfigurationListHelper::class,
                        'REQUIRED' => true,
                        'EDIT_IN_LIST' => false
                    ]
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
            OptionListHelper::class => [
                'BUTTONS' => [
                    'LIST_CREATE_NEW' => [
                        'TEXT' => Loc::getMessage("ADD_NEW_SETTING"),
                    ],
                    'LIST_CREATE_NEW_SECTION' => [
                        'TEXT' => Loc::getMessage("ADD_NEW_CONFIGURATION"),
                    ]
                ]
            ],
            OptionEditHelper::class
        ];
    }
}