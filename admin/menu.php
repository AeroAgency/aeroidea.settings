<?php

use Aeroidea\Settings\Options\AdminInterface\ConfigurationEditHelper;
use Aeroidea\Settings\Options\AdminInterface\OptionEditHelper;
use Aeroidea\Settings\Options\AdminInterface\OptionListHelper;
use Aeroidea\Settings\Options\AdminInterface\ValuesListHelper;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

try {
    if (!Loader::includeModule('digitalwand.admin_helper') || !Loader::includeModule('aeroidea.settings')) {
        return;
    }
} catch (\Bitrix\Main\LoaderException $e) {
}
Loc::loadMessages(__FILE__);
return [
    [
        'parent_menu' => 'global_menu_settings',
        'sort' => 300,
        'icon' => 'fileman_sticker_icon',
        'page_icon' => 'fileman_sticker_icon',
        'text' => Loc::getMessage("CONFIGURATION_SETTING"),
        'url' => OptionListHelper::getUrl(),
        'more_url' => [
            OptionEditHelper::getUrl(),
            ConfigurationEditHelper::getUrl()
        ]
    ],
    [
        'parent_menu' => 'global_menu_settings',
        'sort' => 300,
        'icon' => 'fileman_sticker_icon',
        'page_icon' => 'fileman_sticker_icon',
        'text' => Loc::getMessage("CONFIGURATION"),
        'url' => ValuesListHelper::getUrl()
    ]
];