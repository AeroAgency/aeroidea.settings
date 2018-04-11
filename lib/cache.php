<?php
/**
 * Aero.settings module
 *
 * @category    aero
 * @link        http://aeroindea.ru
 */
namespace Aero\Settings;

/**
 * Модель кэша
 */
class Cache
{
    /**
     * Экземпляр кэша
     *
     * @var \Bitrix\Main\Data\Cache
     */
    protected $core = null;

    /**
     * Идентификатор кэша
     *
     * @var string
     */
    protected $id = '';

    /**
     * Каталог кэша
     *
     * @var string
     */
    protected $dir = '';

    /**
     * Время жизни кэша
     *
     * @var integer
     */
    protected $time = 0;

    /**
     * Конструктор
     *
     * @param mixed $cacheId Идентификатор кэша
     * @param mixed $cacheDir Каталог кэша
     * @param mixed $cacheTime Время жизни кэша
     * @param string $triggerTag Кеш тега
     */
    public function __construct($cacheId, $cacheDir, $cacheTime = 3600, $triggerTag = '')
    {
        $this->id = serialize($cacheId);
        $this->dir = str_replace(array('/', '\\'), \DIRECTORY_SEPARATOR, $cacheDir);
        if($triggerTag) {
            $GLOBALS['CACHE_MANAGER']->StartTagCache($this->dir);
            $GLOBALS['CACHE_MANAGER']->RegisterTag($triggerTag);
            $GLOBALS['CACHE_MANAGER']->EndTagCache();
        }
        $this->time = (int) $cacheTime;
        $this->core = \Bitrix\Main\Data\Cache::createInstance();
    }

    /**
     * Запускает кэширование
     *
     * @return boolean
     */
    public function start()
    {
        $user = new \CUser();
        if ($_REQUEST['clear_cache'] == 'Y' && $user->IsAdmin()) {
            $this->core->clean($this->id, $this->dir);
        }

        return $this->core->startDataCache($this->time, $this->id, $this->dir);
    }

    /**
     * Сохраняет данные в кэш
     *
     * @param mixed $data Данные для кэширования
     * @return void
     */
    public function end($data)
    {
        $this->core->endDataCache($data);
    }

    /**
     * Возвращает ранее закэшированные данные
     *
     * @return mixed
     */
    public function getVars()
    {
        return $this->core->getVars();
    }

    /**
     * Удаляет ранее проинициализированный кэш
     *
     * @return void
     */
    public function abort()
    {
        $this->core->abortDataCache();
    }
}