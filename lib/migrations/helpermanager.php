<?php
namespace Aeroidea\Settings\Migrations;

use Sprint\Migration\Exceptions\HelperException;
use Sprint\Migration\Helpers\AdminIblockHelper;
use Sprint\Migration\Helpers\AgentHelper;
use Sprint\Migration\helpers\DeliveryServiceHelper;
use Sprint\Migration\Helpers\EventHelper;
use Sprint\Migration\Helpers\FormHelper;
use Sprint\Migration\Helpers\HlblockHelper;
use Sprint\Migration\Helpers\IblockHelper;
use Sprint\Migration\Helpers\LangHelper;
use Sprint\Migration\Helpers\MedialibHelper;
use Sprint\Migration\Helpers\OptionHelper;
use Sprint\Migration\Helpers\SiteHelper;
use Sprint\Migration\Helpers\SqlHelper;
use Sprint\Migration\Helpers\UserGroupHelper;
use Sprint\Migration\Helpers\UserOptionsHelper;
use Sprint\Migration\Helpers\UserTypeEntityHelper;
use Aeroidea\Settings\Migrations\Helpers\ConfigurationHelper;
use Sprint\Migration\HelperManager as MigrationHelper;
/**
 * @method IblockHelper             Iblock()
 * @method HlblockHelper            Hlblock()
 * @method AgentHelper              Agent()
 * @method EventHelper              Event()
 * @method LangHelper               Lang()
 * @method SiteHelper               Site()
 * @method UserOptionsHelper        UserOptions()
 * @method UserTypeEntityHelper     UserTypeEntity()
 * @method UserGroupHelper          UserGroup()
 * @method OptionHelper             Option()
 * @method FormHelper               Form()
 * @method DeliveryServiceHelper    DeliveryService()
 * @method SqlHelper                Sql()
 * @method MedialibHelper           Medialib()
 * @method AdminIblockHelper        AdminIblock()
 * @method ConfigurationHelper      Configuration()
 */
class HelperManager extends MigrationHelper
{
    private $cache = [];

    private $directoryAddressCustomHelpers = "";

    private static $instance = null;

    private $registered = [];

    /**
     * @return HelperManager
     */
    public static function getInstance()
    {
        if (!isset(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * @param $directoryAddress
     */
    public function setHelpersCustomDirectory($directoryAddress)
    {
        $this->directoryAddressCustomHelpers = $directoryAddress;
    }

    /**
     * @param $name
     * @return mixed|\Sprint\Migration\Helper
     * @throws HelperException
     */
    protected function callHelper($name)
    {
        try {
            return $this->callHelperByName($name);
        } catch (HelperException $e) {
            if(!empty($this->directoryAddressCustomHelpers)){
                return $this->callHelperByName($name, $this->directoryAddressCustomHelpers);
            }
        }
    }

    /**
     * @param $name
     * @param string $address
     * @return mixed
     * @throws HelperException
     */
    protected function callHelperByName($name, $address = '\\Sprint\\Migration\\Helpers\\')
    {
        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        $helperClass = $address . $name . 'Helper';
        if (class_exists($helperClass)) {
            $this->cache[$name] = new $helperClass;
            return $this->cache[$name];
        }

        if (isset($this->registered[$name])) {
            $helperClass = $this->registered[$name];
            if (class_exists($helperClass)) {
                $this->cache[$name] = new $helperClass;
                return $this->cache[$name];
            }
        }
        Throw new HelperException("Helper $name not found");
    }
}