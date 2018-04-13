<?php
namespace Aeroidea\Settings\Options;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\Event;
use Bitrix\Main\Entity\EventResult;
use Bitrix\Main\Entity\Validator\Unique;
use Bitrix\Main\Localization\Loc;


Loc::loadMessages(__FILE__);
/**
 * Модель полей настроек.
 */
class ConfigurationTable extends DataManager
{
    /**
     * @inheritdoc
     */
    public static function getTableName()
    {
        return 'd_ah_configuration';
    }
    /**
     * @inheritdoc
     */
    public static function getMap()
    {
        return [
            'ID' => [
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true,
            ],
            'CODE' => [
                'data_type' => 'string',
                'title' => Loc::getMessage("SYMBOL_CODE"),
                'required' => true
            ],
            'PARENT_ID' => [
                'data_type' => 'integer',
                'title' => Loc::getMessage("PARENT_ID_CONFIGURATION")
            ],
            'TITLE' => [
                'data_type' => 'string',
                'title' => Loc::getMessage("TITLE")
            ],
            'PARENT_CONFIGURATION' => [
                'data_type' => ConfigurationTable::class,
                'reference' => ['=this.PARENT_ID' => 'ref.ID'],
            ]
        ];
    }

    public function validationCode()
    {
        return array(
            new Unique(),
        );
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
            $rs = OptionTable::getList(
                [
                    'filter' => ['CONFIGURATION_ID' => $id],
                    'select' => ['ID']
                ]
            );
            while ($ar = $rs->fetch()) {
                OptionTable::delete($ar['ID']);
            }
        }
        return $result;
    }

}