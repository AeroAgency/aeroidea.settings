<?php
namespace Aero\Settings\Options\AdminInterface;

use Bitrix\Main\Localization\Loc;
use DigitalWand\AdminHelper\Helper\AdminInterface;
use DigitalWand\AdminHelper\Widget\DateTimeWidget;
use DigitalWand\AdminHelper\Widget\NumberWidget;
use DigitalWand\AdminHelper\Widget\OrmElementWidget;
use DigitalWand\AdminHelper\Widget\StringWidget;
use DigitalWand\AdminHelper\Widget\UserWidget;

Loc::loadMessages(__FILE__);
/**
 * Описание интерфейса (табок и полей) админки конфигурации настроек.
 *
 * {@inheritdoc}
 */
class ConfigurationAdminInterface extends AdminInterface
{
    /**
     * @inheritdoc
     */
    public function dependencies()
    {
        return [OptionAdminInterface::class];
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        return [
            'MAIN' => [
                'NAME' => Loc::getMessage("CONFIGURATION"),
                'FIELDS' => [
                    'ID' => array(
                        'WIDGET' => new NumberWidget(),
                        'READONLY' => true,
                        'FILTER' => false,
                        'HIDE_WHEN_CREATE' => true,
                        'EDIT_IN_LIST' => false
                    ),
                    'TITLE' => array(
                        'WIDGET' => new StringWidget(),
                        'SIZE' => '80',
                        'FILTER' => '%',
                        'REQUIRED' => true,
                        'SECTION_LINK' => true,
                        'EDIT_IN_LIST' => false
                    ),
                    'CODE' => array(
                        'WIDGET' => new StringWidget(),
                        'SIZE' => '80',
                        'FILTER' => '%',
                        'REQUIRED' => true,
                        'SECTION_LINK' => true,
                        'EDIT_IN_LIST' => false
                    )
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
            ConfigurationListHelper::class,
            ConfigurationEditHelper::class
        ];
    }
}