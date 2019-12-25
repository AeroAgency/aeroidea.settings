<?php
namespace Aeroidea\Settings\Migrations\Builders;

use Sprint\Migration\VersionBuilder;
use Aeroidea\Settings\Options\OptionTable;
use Aeroidea\Settings\Options\ConfigurationTable;
use Sprint\Migration\Locale;
use Aeroidea\Settings\Migrations\HelperManager;
use Sprint\Migration\Exceptions\HelperException;
use Sprint\Migration\Exceptions\RebuildException;

class ConfigurationMigrationBuilder extends VersionBuilder
{
    protected function isBuilderEnabled()
    {
        return true;
    }

    protected function initialize()
    {
        $this->setTitle("Создать миграцию для конфигурации");
        $this->setDescription("");
        $this->addVersionFields();
    }

    /**
     * @throws HelperException
     * @throws RebuildException
     */
    protected function execute()
    {
        $helper = HelperManager::getInstance();
        $helper->setHelpersCustomDirectory('\\Aeroidea\\Settings\\Migrations\\Helpers\\');

        $this->addField('configuration_block_id', [
            'title' => "Выбирите конфигурацию",
            'placeholder' => '',
            'width' => 250,
            'select' => $this->getConfigurationsStructure(),
        ]);
        $this->addField('what', [
            'title' => "Что переносим?",
            'width' => 250,
            'multiple' => 1,
            'value' => [],
            'select' => [
                [
                    'title' => 'Конфигурацию',
                    'value' => 'configurationSettings',
                ],
                [
                    'title' => 'Настройки конфигурации',
                    'value' => 'configurationOptions',
                ],
            ],
        ]);

        $configurationId =  $this->getFieldValue('configuration_block_id');
        if (empty($configurationId)) {
            $this->rebuildField('configuration_block_id');
        }
        $what = $this->getFieldValue('what');
        if (!empty($what)) {
            $what = is_array($what) ? $what : [$what];
        } else {
            $this->rebuildField('what');
        }
        $configurationExport = [];
        $optionsExport = [];
        $configurationCode = "";

        if(in_array('configurationSettings', $what))
        {
            $configurationExport = $helper->Configuration()->exportConfiguration($configurationId);
        }
        if (in_array('configurationOptions', $what)) {
            $this->addField('options_ids', [
                'title' => "Выбирете настройки",
                'width' => 250,
                'multiple' => 1,
                'value' => [],
                'select' => $this->getOptions($configurationId),
            ]);
            $optionsIds = $this->getFieldValue('options_ids');
            if (!empty($optionsIds)) {
                $configurationCode = $helper->Configuration()->getConfigurationById($configurationId)["CODE"];

                $optionsIds = is_array($optionsIds) ? $optionsIds : [$optionsIds];
                $optionsExport = $helper->Configuration()->exportOptions($optionsIds);
            } else {
                $this->rebuildField('property_ids');
            }
        }
        $this->createVersionFile($this->getModuleDir() .'/migrations/templates/ConfigurationExport.php',
            [
            'configurationExport' => $configurationExport,
            'optionsExport' => $optionsExport,
            'configurationCode' => $configurationCode
            ]
        );

    }

    /**
     * @return string
     */
    public function getModuleDir()
    {
        if (is_file($_SERVER['DOCUMENT_ROOT'] . '/local/modules/aeroidea.settings/include.php')) {
            return $_SERVER['DOCUMENT_ROOT'] . '/local/modules/aeroidea.settings/lib';
        } else {
            return $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/aeroidea.settings/lib';
        }
    }

    /**
     * @param string $configurationId
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected function getOptions($configurationId)
    {

        $result = OptionTable::getList([
            "filter" => ["CONFIGURATION_ID" => $configurationId]
        ])->fetchAll();
        $structure = [];
        foreach ($result as $config){
            $structure[] =
                [
                    "title" => $config["TITLE"],
                    "value" => $config["ID"]
                ];
        }
        return $structure;
    }


    protected function getConfigurationsStructure()
    {
        $blocks = ConfigurationTable::getList([])->fetchAll();
        $newBlocks = [];
        foreach ($blocks as $block)
        {
            $newBlocks[] = [
                "title" => $block["TITLE"],
                "value" => $block["ID"]
            ];
        }
        return $newBlocks;
    }
}