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
class ImageWidget extends BaseFileWidget
{
    protected static $defaults = array(
        'IMAGE' => true,
        'DESCRIPTION_FIELD' => false,
        'EDIT_IN_LIST' => false,
        'FILTER' => false,
        'UPLOAD' => true,
        'MEDIALIB' => true,
        'FILE_DIALOG' => true,
        'CLOUD' => true,
        'DELETE' => true,
        'EDIT' => true,
    );

    /**
     * {@inheritdoc}
     */
    protected function getEditHtml()
    {
        if (class_exists('\Bitrix\Main\UI\FileInput', true) && $this->getSettings('IMAGE') === true) {
            $html = FileInput::createInstance(array(
                'name' => $this->getEditInputName('_FILE'),
                'description' => $this->getSettings('DESCRIPTION_FIELD'),
                'upload' => $this->getSettings('UPLOAD'),
                'allowUpload' => 'I',
                'medialib' => $this->getSettings('MEDIALIB'),
                'fileDialog' => $this->getSettings('FILE_DIALOG'),
                'cloud' => $this->getSettings('CLOUD'),
                'delete' => $this->getSettings('DELETE'),
                'edit' => $this->getSettings('EDIT'),
                'maxCount' => 1
            ))->show($this->getValue());
        } else {
            $html = \CFileInput::Show($this->getEditInputName('_FILE'), $this->getValue(),
                array(
                    'IMAGE' => $this->getSettings('IMAGE') === true ? 'Y' : 'N',
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
        }

        if ($this->getValue()) {
            $html .= '<input type="hidden" name="' . $this->getEditInputName() . '" value=' . $this->getValue() . '>';
        }

        return $html;
    }
}