<?php
namespace Aero\Settings\Options\AdminInterface;

use Bitrix\Main\Localization\Loc;
use DigitalWand\AdminHelper\Helper\AdminListHelper as DigitalAdminListHelper;
use DigitalWand\AdminHelper\Helper\AdminSectionEditHelper;

Loc::loadMessages(__FILE__);
/**
 * Описание интерфейса (табок и полей) админки конфигурации настроек.
 *
 * {@inheritdoc}
 */
class AdminListHelper extends DigitalAdminListHelper
{
    /**
     * Цель создания этого класса при помощи наследования пофиксить баг
     *
     * Основной цикл отображения списка. Этапы:
     * <ul>
     * <li> Вывод заголовков страницы </li>
     * <li> Определение списка видимых колонок и колонок, участвующих в выборке. </li>
     * <li> Создание виджета для каждого поля выборки </li>
     * <li> Модификация параметров запроса каждым из виджетов </li>
     * <li> Выборка данных </li>
     * <li> Вывод строк таблицы. Во время итерации по строкам возможна модификация данных строки. </li>
     * <li> Отрисовка футера таблицы, добавление контекстного меню </li>
     * </ul>
     *
     * @param array $sort Настройки сортировки.
     *
     * @see AdminListHelper::getList();
     * @see AdminListHelper::getMixedData();
     * @see AdminListHelper::modifyRowData();
     * @see AdminListHelper::addRowCell();
     * @see AdminListHelper::addRow();
     * @see HelperWidget::changeGetListOptions();
     */
    public function buildList($sort)
    {
        $this->setContext(AdminListHelper::OP_GET_DATA_BEFORE);

        $headers = $this->arHeader;

        $sectionEditHelper = static::getHelperClass(AdminSectionEditHelper::className());

        if ($sectionEditHelper) { // если есть реализация класса AdminSectionEditHelper, значит используются разделы
            $sectionHeaders = $this->getSectionsHeader();
            foreach ($sectionHeaders as $sectionHeader) {
                foreach ($headers as $i => $elementHeader) {
                    if ($sectionHeader['id'] == $elementHeader['id']) {
                        unset($headers[$i]);
                    }
                }
            }
            $headers = array_merge($headers, $sectionHeaders);
        }

        // сортировка столбцов с сохранением исходной позиции в
        // массиве для развнозначных элементов
        // массив $headers модифицируется
        $this->mergeSortHeader($headers);

        $this->list->AddHeaders($headers);
        $visibleColumns = $this->list->GetVisibleHeaderColumns();

        if ($sectionEditHelper) {
            $modelClass = $this->getModel();
            $elementFields = array_keys($modelClass::getEntity()->getFields());
            $sectionsVisibleColumns = array();
            foreach ($visibleColumns as $k => $v) {
                if (isset($this->sectionFields[$v])) {
                    if(!in_array($v, $elementFields)){
                        unset($visibleColumns[$k]);
                    }
                    $sectionsVisibleColumns[] = $v;
                }
            }
            $visibleColumns = array_values($visibleColumns);
            $visibleColumns = array_merge($visibleColumns, array_keys($this->tableColumnsMap));
        }

        $className = static::getModel();
        $visibleColumns[] = $this->pk();

        $sectionsVisibleColumns[] = $this->sectionPk();
        $raw = array(
            'SELECT' => $visibleColumns,
            'FILTER' => $this->arFilter,
            'SORT' => $sort
        );

        foreach ($this->fields as $name => $settings) {
            if ((isset($settings['VIRTUAL']) AND $settings['VIRTUAL'] == true)) {
                $key = array_search($name, $visibleColumns);
                unset($visibleColumns[$key]);
                unset($this->arFilter[$name]);
                unset($sort[$name]);
            }
            if (isset($settings['FORCE_SELECT']) AND $settings['FORCE_SELECT'] == true) {
                $visibleColumns[] = $name;
            }
        }

        $visibleColumns = array_unique($visibleColumns);
        $sectionsVisibleColumns = array_unique($sectionsVisibleColumns);

        // Поля для селекта (перевернутый массив)
        $listSelect = array_flip($visibleColumns);
        foreach ($this->fields as $code => $settings) {
            $widget = $this->createWidgetForField($code);
            $widget->changeGetListOptions($this->arFilter, $visibleColumns, $sort, $raw);
            // Множественные поля не должны быть в селекте
            if (!empty($settings['MULTIPLE'])) {
                unset($listSelect[$code]);
            }
        }
        // Поля для селекта (множественные поля отфильтрованы)
        $listSelect = array_flip($listSelect);

        if ($sectionEditHelper) // Вывод разделов и элементов в одном списке
        {
            $mixedData = $this->getMixedData($sectionsVisibleColumns, $visibleColumns, $sort, $raw);
            $res = new \CDbResult;
            $res->InitFromArray($mixedData);
            $res = new \CAdminResult($res, $this->getListTableID());
            $res->nSelectedCount = $this->totalRowsCount;
            // используем кастомный NavStart что бы определить правильное количество страниц и элементов в списке
            $this->customNavStart($res);
            $this->list->NavText($res->GetNavPrint(Loc::getMessage("PAGES")));
            while ($data = $res->NavNext(false)) {
                $this->modifyRowData($data);
                if ($data['IS_SECTION']) // для разделов своя обработка
                {
                    list($link, $name) = $this->getRow($data, $this->getHelperClass(AdminSectionEditHelper::className()));
                    $row = $this->list->AddRow('s' . $data[$this->pk()], $data, $link, $name);
                    foreach ($this->sectionFields as $code => $settings) {
                        if (in_array($code, $sectionsVisibleColumns)) {
                            $this->addRowSectionCell($row, $code, $data);
                        }
                    }
                    $row->AddActions($this->getRowActions($data, true));
                }
                else // для элементов своя
                {
                    $this->modifyRowData($data);
                    list($link, $name) = $this->getRow($data);
                    // объединение полей элемента с полями раздела
                    foreach ($this->tableColumnsMap as $elementCode => $sectionCode) {
                        if (isset($data[$elementCode])) {
                            $data[$sectionCode] = $data[$elementCode];
                        }
                    }
                    $row = $this->list->AddRow($data[$this->pk()], $data, $link, $name);
                    foreach ($this->fields as $code => $settings) {
                        $this->addRowCell($row, $code, $data,
                            isset($this->tableColumnsMap[$code]) ? $this->tableColumnsMap[$code] : false);
                    }
                    $row->AddActions($this->getRowActions($data));
                }
            }
        }
        else // Обычный вывод элементов без использования разделов
        {
            $res = $this->getData($className, $this->arFilter, $listSelect, $sort, $raw);
            $res = new \CAdminResult($res, $this->getListTableID());
            $res->NavStart();
            $this->list->NavText($res->GetNavPrint(Loc::getMessage("PAGES")));
            while ($data = $res->NavNext(false)) {
                $this->modifyRowData($data);
                list($link, $name) = $this->getRow($data);
                $row = $this->list->AddRow($data[$this->pk()], $data, $link, $name);
                foreach ($this->fields as $code => $settings) {
                    $this->addRowCell($row, $code, $data);
                }
                $row->AddActions($this->getRowActions($data));
            }
        }

        $this->list->AddFooter($this->getFooter($res));
        $this->list->AddGroupActionTable($this->getGroupActions(), $this->groupActionsParams);
        $this->list->AddAdminContextMenu($this->getContextMenu());

        $this->list->BeginPrologContent();
        echo $this->prologHtml;
        $this->list->EndPrologContent();

        $this->list->BeginEpilogContent();
        echo $this->epilogHtml;
        $this->list->EndEpilogContent();

        // добавляем ошибки в CAdminList для режимов list и frame
        if(in_array($_GET['mode'], array('list','frame')) && is_array($this->getErrors())) {
            foreach($this->getErrors() as $error) {
                $this->list->addGroupError($error);
            }
        }

        $this->list->CheckListMode();
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