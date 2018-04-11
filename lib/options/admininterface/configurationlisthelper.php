<?php
namespace Aero\Settings\Options\AdminInterface;

use Aero\Settings\Options\ConfigurationTable;
use Bitrix\Main\Localization\Loc;
use DigitalWand\AdminHelper\Helper\AdminSectionListHelper;

Loc::loadMessages(__FILE__);
/**
 * Хелпер описывает интерфейс, выводящий список настроек конфигурации.
 *
 * {@inheritdoc}
 */

class ConfigurationListHelper extends AdminSectionListHelper
{
    protected static $model = ConfigurationTable::class;

    /**
     * @var string заголовок
     */
    public $title;

    /**
     * ConfigurationListHelper constructor.
     * @param array $fields
     * @param bool $isPopup
     * @throws \Bitrix\Main\ArgumentException
     */
    public function __construct(array $fields, $isPopup = false)
    {
        parent::__construct($fields, $isPopup);
        parent::setTitle($this->title);
        $this->title = Loc::getMessage("CONFIGURATIONS_LIST");
    }
}