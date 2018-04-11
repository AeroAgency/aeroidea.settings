<?php

namespace Aero\Settings\Options\Widget;

use DigitalWand\AdminHelper\Widget\CheckboxWidget;


/**
 * Виджет текстового чекбокса
 * Нужен для виртуальных полей, чтобы исключить проверку в модели
 */
class StringCheckboxWidget extends CheckboxWidget
{
    /**
     * Получить тип чекбокса по типу поля.
     *
     * @return mixed
     */
    public function getCheckboxType()
    {
        return 'string';
    }
}