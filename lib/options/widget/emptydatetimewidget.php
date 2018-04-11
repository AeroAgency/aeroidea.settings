<?php

namespace Aero\Settings\Options\Widget;

use DigitalWand\AdminHelper\Widget\DateTimeWidget;


/**
 * Виджет для календаря
 * Если дата не заполнено, то поле пустое
 */
class EmptyDateTimeWidget extends DateTimeWidget
{
    /**
     * Генерирует HTML для редактирования поля
     * @see AdminEditHelper::showField();
     * @return mixed
     */
    protected function getEditHtml()
    {
        if ($this->getValue()) {
            return \CAdminCalendar::CalendarDate($this->getEditInputName(), ConvertTimeStamp(strtotime($this->getValue()), "FULL"), 10, true);
        }
        return \CAdminCalendar::CalendarDate($this->getEditInputName(), '', 10, true);
    }

    /**
     * Сконвертируем дату в формат Mysql
     * @return boolean
     */
    public function processEditAction()
    {
        try {
            if ($this->getValue()) {
                $this->setValue(new \Bitrix\Main\Type\Datetime($this->getValue()));
            }
        } catch (\Exception $e) {
        }
        if (!$this->checkRequired()) {
            $this->addError('REQUIRED_FIELD_ERROR');
        }
    }
}