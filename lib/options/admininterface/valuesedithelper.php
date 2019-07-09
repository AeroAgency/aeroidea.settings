<?php
namespace Aeroidea\Settings\Options\AdminInterface;

use Aeroidea\Settings\Config;
use Aeroidea\Settings\Options\ConfigurationTable;
use Aeroidea\Settings\Options\ValuesTable;
use Aeroidea\Settings\Util;
use Bitrix\Main\Localization\Loc;
use DigitalWand\AdminHelper\EntityManager;
use DigitalWand\AdminHelper\Helper\AdminEditHelper;

Loc::loadMessages(__FILE__);
/**
 * Хелпер описывает интерфейс, выводящий список настроек конфигурации.
 *
 * {@inheritdoc}
 */

class ValuesEditHelper extends AdminEditHelper
{
    protected static $model = ConfigurationTable::class;

    protected $configId;

    /**
     * @var string заголовок
     */
    public $title;

    /**
     * ConfigurationListHelper constructor.
     * @param array $fields
     * @param bool $isPopup
     */
    public function __construct(array $fields, $isPopup = false)
    {
        parent::__construct($fields, $isPopup);
        parent::setTitle($this->title);
        $this->title = Loc::getMessage("TITLE");
    }

    /**
     * Функция загрузки элемента из БД. Можно переопределить, если требуется сложная логика и нет возможности
     * определить её в модели.
     *
     * @param array $select
     *
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     * @throws \Exception
     * @api
     */
    protected function loadElement($select = array())
    {
        if ($this->getPk() !== null) {
            $className = static::getModel();
            $result = $className::getById($this->getPk());
            $resultData = $result->fetch();

            $arrAddData = ValuesTable::getConfigurationOptionsValues($this->getPk());
            if(!empty($arrAddData)) {
                foreach ($arrAddData as $arrAddDataElement) {
                    $resultData[$arrAddDataElement['CODE']] = $arrAddDataElement['VALUE'];
                }
            }
            return $resultData;
        }
        return false;
    }

    /**
     * Сохранение элемента. Можно переопределить, если требуется сложная логика и нет возможности определить её
     * в модели.
     *
     * Операциями сохранения модели занимается EntityManager.
     *
     * @param bool $id
     *
     * @return \Bitrix\Main\Entity\AddResult|\Bitrix\Main\Entity\UpdateResult
     *
     * @throws \Exception
     *
     * @see EntityManager
     *
     * @api
     */
    protected function saveElement($id = null)
    {
        /** @var EntityManager $entityManager */
        $configData = Util::getIndexedArray(ValuesTable::getConfigurationOptionsValues($this->getPk()), 'CODE');

        foreach ($this->data as $key => $value) {
            if($configData[$key]) {
                unset($this->data[$key]);
                unset($this->data[$key.'_TEXT_TYPE']);
                $configData[$key]['VALUE'] = $value;
            }
        }

        /**
         * Делаем сохранение в 2 этапа:
         *
         * 1. Сохраняем связанные модели: значения опций
         *
         * 2. Сохраняем базовую модель (конфигурацию)
         */

        $model = ValuesTable::class;
        foreach ($configData as $configDataItem) {
            $data = [
                'OPTION_ID' => $configDataItem['ID'],
                'VALUE' => $configDataItem['VALUE']
            ];
            $entityManager = new static::$entityManager($model, $data, $configDataItem['VALUE_ID'], $this);
            $saveResult = $entityManager->save();
            $this->addNotes($entityManager->getNotes());
            if(!$saveResult->isSuccess()) {
                return $saveResult;
            }
        }
        if(!$this->data) {
            $this->data = $this->loadElement();
        }
        $entityManager = new static::$entityManager(static::getModel(), $this->data, $id, $this);
        $saveResult = $entityManager->save();
        $this->addNotes($entityManager->getNotes());
        if(!$saveResult->isSuccess()) {
            return $saveResult;
        }
        Config::clearCache();
        return $saveResult;
    }

    /**
     * Удаление элемента. Можно переопределить, если требуется сложная логика и нет возможности определить её в модели.
     *
     * @param $id
     *
     * @return bool|\Bitrix\Main\Entity\DeleteResult
     *
     * @throws \Exception
     *
     * @api
     */
    protected function deleteElement($id)
    {
        if (!$this->hasDeleteRights()) {
            $this->addErrors(Loc::getMessage('DIGITALWAND_ADMIN_HELPER_EDIT_DELETE_FORBIDDEN'));

            return false;
        }

        /** @var EntityManager $entityManager */
        $entityManager = new static::$entityManager(static::getModel(), empty($this->data) ? array() : $this->data, $id, $this);

        $deleteResult = $entityManager->delete();
        $this->addNotes($entityManager->getNotes());

        return $deleteResult;
    }

    /**
     * Возвращает верхнее меню страницы.
     * По-умолчанию две кнопки:
     * <ul>
     * <li> Возврат в список</li>
     * <li> Удаление элемента</li>
     * </ul>
     *
     * Добавляя новые кнопки, нужно указывать параметр URl "action", который будет обрабатываться в
     * AdminEditHelper::customActions()
     *
     * @param bool $showDeleteButton Управляет видимостью кнопки удаления элемента.
     *
     * @return array
     *
     * @see AdminEditHelper::$menu
     * @see AdminEditHelper::customActions()
     *
     * @api
     */
    protected function getMenu($showDeleteButton = true)
    {

        $menu = parent::getMenu($showDeleteButton);
        unset($menu[2]);
        return $menu;
    }
}