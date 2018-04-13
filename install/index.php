<?

use Bitrix\Main\Localization\Loc;

/**
 * Aeroidea settings module
 *
 * @category    Aeroidea
 * @link        http://aeroidea.ru
 */
Class aeroidea_settings extends CModule
{
    /**
     * ID модуля
     * @var string
     */
    public $MODULE_ID = 'aeroidea.settings';

    /**
     * Версия модуля
     * @var string
     */
    public $MODULE_VERSION = '';

    /**
     * Дата выхода версии
     * @var string
     */
    public $MODULE_VERSION_DATE = '';

    /**
     * Название модуля
     * @var string
     */
    public $MODULE_NAME;

    /**
     * Описание модуля
     * @var string
     */
    public $MODULE_DESCRIPTION;

    /**
     * Имя партнера
     * @var string
     */
    public $PARTNER_NAME = "AEROIDEA";

    /**
     * Ссылка на сайт партнера
     * @var string
     */
    public $PARTNER_URI = "http://aeroidea.ru";

    /**
     * Обработчики событий
     * @var array
     */
    public $eventHandlers = [];

    /**
     * Конструктор модуля
     */
    public function __construct()
    {
        $version = include __DIR__ . '/version.php';

        $this->MODULE_VERSION = $version['VERSION'];
        $this->MODULE_VERSION_DATE = $version['VERSION_DATE'];

        $this->eventHandlers = [
            [
                'main',
                'OnPageStart',
                '\Aeroidea\Tools\Module',
                'onPageStart',
            ]
        ];
        $this->MODULE_NAME = Loc::getMessage("MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("MODULE_DESC");
    }

    /**
     * Устанавливает события модуля
     *
     * @return boolean
     */
    public function installEvents()
    {
        $eventManager = \Bitrix\Main\EventManager::getInstance();

        foreach ($this->eventHandlers as $handler) {
            $eventManager->registerEventHandler($handler[0], $handler[1], $this->MODULE_ID, $handler[2], $handler[3]);
        }

        return true;
    }

    /**
     * Удаляет события модуля
     *
     * @return boolean
     */
    public function unInstallEvents()
    {
        $eventManager = \Bitrix\Main\EventManager::getInstance();

        foreach ($this->eventHandlers as $handler) {
            $eventManager->unRegisterEventHandler($handler[0], $handler[1], $this->MODULE_ID, $handler[2], $handler[3]);
        }

        return true;
    }

    /**
     * Устанавливает модуль
     *
     * @return void
     */
    public function DoInstall()
    {
        if ($this->installEvents() && $this->InstallDB()) {
            \Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);
        }
    }

    /**
     * Удаляет модуль
     *
     * @return void
     */
    public function DoUninstall()
    {
        if ($this->unInstallEvents()) {
            \Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);
        }

        $this->UnInstallDB();
    }

    /**
     * @return string Возвращает директорию с файлами для БД
     */
    private function getDataBaseDir()
    {
        $moduleDir = explode('/', __DIR__);
        array_pop($moduleDir);
        $moduleDir = implode('/', $moduleDir);
        $sourceRoot = $moduleDir . '/install/db/';
        return $sourceRoot;
    }

    function InstallDB()
    {
        global $DB;
        $DB->RunSQLBatch($this->getDataBaseDir() . "install.sql");
        return true;
    }
    function UnInstallDB()
    {
        global $DB;
        $DB->RunSQLBatch($this->getDataBaseDir() . "uninstall.sql");
        return true;
    }
}
