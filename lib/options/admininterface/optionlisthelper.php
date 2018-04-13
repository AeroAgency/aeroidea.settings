<?php
namespace Aeroidea\Settings\Options\AdminInterface;

use Aeroidea\Settings\Options\OptionTable;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
/**
 * Хелпер описывает интерфейс, выводящий список настроек конфигурации.
 *
 * {@inheritdoc}
 */

class OptionListHelper extends AdminListHelper
{
    protected static $model = OptionTable::class;

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
        $this->title = Loc::getMessage("SETTINGS_LIST");
    }

    /**
     * Возвращает массив с настройками групповых действий над списком.
     *
     * @return array
     *
     * @api
     */
    protected function getGroupActions()
    {
        $result = [];
        return $result;
    }


}