<?php
namespace Aeroidea\Settings\Options\AdminInterface;

use Aeroidea\Settings\Options\ConfigurationTable;
use Bitrix\Main\Localization\Loc;
use DigitalWand\AdminHelper\Helper\AdminSectionEditHelper;

Loc::loadMessages(__FILE__);
/**
 * Хелпер описывает интерфейс, выводящий форму редактирования настройки конфигурации.
 *
 * {@inheritdoc}
 */
class ConfigurationEditHelper extends AdminSectionEditHelper
{
    protected static $model = ConfigurationTable::class;

    /**
     * @inheritdoc
     */
    public function setTitle($title)
    {
        /*if (!empty($this->data)) {
            $title = Loc::getMessage('DEMO_AH_NEWS_EDIT_TITLE', array('#ID#' => $this->data[$this->pk()]));
        }
        else {
            $title = Loc::getMessage('DEMO_AH_NEWS_NEW_TITLE');
        }*/
    }
}