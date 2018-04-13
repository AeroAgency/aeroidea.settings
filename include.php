<?php
/**
 * Aeroidea.settings module
 *
 * @category	Aeroidea
 * @link		http://aeroidea.ru
 */


namespace Aeroidea\Settings;

/**
 * Базовый каталог модуля
 */
use Bitrix\Main\Event;
const BASE_DIR = __DIR__;

$event = new Event('aeroidea.settings', 'onModuleInclude');
$event->send();