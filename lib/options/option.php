<?php
namespace Aeroidea\Settings\Options;
use Aeroidea\Main\Util;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\Event;
use Bitrix\Main\Entity\EventResult;
use Bitrix\Main\Entity\Validator\Unique;
use Bitrix\Main\Localization\Loc;


Loc::loadMessages(__FILE__);
/**
 * Модель полей настроек.
 */
class OptionTable extends DataManager
{
    /**
     * @inheritdoc
     */
    public static function getTableName()
    {
        return 'd_ah_option';
    }
    /**
     * @inheritdoc
     */
    public static function getMap()
    {
        return [
            'ID' => array(
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true
            ),
            'CONFIGURATION_ID' => [
                'data_type' => 'integer',
                'title' => Loc::getMessage("CONFIGURATION_ID"),
                'required' => true,
            ],
            'CODE' => [
                'data_type' => 'string',
                'title' => Loc::getMessage("SYMBOL_CODE")
            ],
            'TYPE' => [
                'data_type' => 'string',
                'title' => Loc::getMessage("TYPE")
            ],
            'TITLE' => [
                'data_type' => 'string',
                'title' => Loc::getMessage("TITLE")
            ],
            'MULTIPLE' => [
                'data_type' => 'string',
                'title' => Loc::getMessage("MULTIPLE"),
            ],
            'CONFIGURATION' => [
                'data_type' => ConfigurationTable::class,
                'reference' => ['=this.CONFIGURATION_ID' => 'ref.ID'],
            ]
        ];
    }

    public function validationCode()
    {
        return [
            new Unique(),
        ];
    }

    public static function getFilePath()
    {
        return __FILE__;
    }

    public static function onAfterDelete(Event $event)
    {
        $result = new EventResult();
        $data = $event->getParameter("id");
        $id = $data['ID'];
        if($id) {
            $rs = ValuesTable::getList(
                [
                    'filter' => ['OPTION_ID' => $id],
                    'select' => ['ID']
                ]
            );
            while ($ar = $rs->fetch()) {
                ValuesTable::delete($ar['ID']);
            }
        }
        return $result;
    }
}