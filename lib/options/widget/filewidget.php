<?php

namespace Aeroidea\Settings\Options\Widget;

use Bitrix\Main\UI\FileInput;
use DigitalWand\AdminHelper\Widget\FileWidget as BaseFileWidget;

/**
 * Для множественного поля в таблице должен быть столбец FILE_ID.
 * Настройки класса:
 * <ul>
 * <li><b>DESCRIPTION_FIELD</b> - bool нужно ли поле описания</li>
 * <li><b>MULTIPLE</b> - bool является ли поле множественным</li>
 * <li><b>IMAGE</b> - bool отображать ли изображение файла, для старого вида отображения</li>
 * </ul>
 */
class FileWidget extends BaseFileWidget
{
    /**
     * {@inheritdoc}
     */
    protected function getEditHtml()
    {
        $html = \CFileInput::Show($this->getEditInputName('_FILE'), $this->getValue(),
            array(
                'IMAGE' => 'N',
                'PATH' => 'Y',
                'FILE_SIZE' => 'Y',
                'ALLOW_UPLOAD' => 'I',
            ), array(
                'upload' => $this->getSettings('UPLOAD'),
                'medialib' => $this->getSettings('MEDIALIB'),
                'file_dialog' => $this->getSettings('FILE_DIALOG'),
                'cloud' => $this->getSettings('CLOUD'),
                'del' => $this->getSettings('DELETE'),
                'description' => $this->getSettings('DESCRIPTION_FIELD'),
            )
        );

        if ($this->getValue()) {
            $html .= '<input type="hidden" name="' . $this->getEditInputName() . '" value=' . $this->getValue() . '>';
        }

        return $html;
    }
}