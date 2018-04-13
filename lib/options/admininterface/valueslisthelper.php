<?php
namespace Aeroidea\Settings\Options\AdminInterface;

use Aeroidea\Settings\Options\ConfigurationTable;
use Aeroidea\Settings\Options\OptionValueTable;
use Aeroidea\Settings\Options\ValuesTable;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
/**
 * Хелпер описывает интерфейс, выводящий список настроек конфигурации.
 *
 * {@inheritdoc}
 */

class ValuesListHelper extends AdminListHelper
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
        $this->title = Loc::getMessage("CONFIGURATIONS");
    }

    /**
     * Возвращает массив со списком действий при клике правой клавишей мыши на строке таблицы
     * По-умолчанию:
     * <ul>
     * <li> Редактировать элемент </li>
     * <li> Удалить элемент </li>
     * <li> Если это всплывающее окно - запустить кастомную JS-функцию. </li>
     * </ul>
     *
     * @param array $data Данные текущей строки.
     * @param boolean $section Признак списка для раздела.
     *
     * @return array
     *
     * @see CAdminListRow::AddActions
     *
     * @api
     */
    protected function getRowActions($data, $section = false)
    {
        $actions = parent::getRowActions($data, $section);
        unset($actions['delete']);
        return $actions;
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

    protected function getContextMenu()
    {
        return [];
    }
}