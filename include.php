<?php
/**
 * Aero.settings module
 *
 * @category	Aeroidea
 * @link		http://aeroidea.ru
 */


namespace Aero\Settings;

/**
 * Базовый каталог модуля
 */
use Bitrix\Main\Event;
const BASE_DIR = __DIR__;

$event = new Event('aero.settings', 'onModuleInclude');
$event->send();