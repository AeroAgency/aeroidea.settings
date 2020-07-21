<?php
/**
 * @var $version
 * @var $description
 * @var $extendUse
 * @var $extendClass
 */
?><?echo "<?php\n" ?>

namespace Sprint\Migration;

use Aeroidea\Settings\Migrations\HelperManager;
<?echo $extendUse?>

class <?php echo $version ?> extends <?php echo $extendClass ?>

{
    protected $description = "<?php echo $description ?>";

    /**
    * @throws Exceptions\HelperException
    * @return bool|void
    */
    public function up()
    {
        $helper = HelperManager::getInstance();
        $helper->setHelpersCustomDirectory('\\Aeroidea\\Settings\\\Migrations\\Helpers\\');
        <? if (!empty($configurationExport)): ?>
        $configId = $helper->Configuration()->saveConfiguration(<?echo var_export($configurationExport, 1) ?>);
        <? endif; ?>
        <? if (!empty($configurationCode)): ?>
        $configId = $helper->Configuration()->getConfigurationByCodeIfExists("<?echo $configurationCode ?>")["ID"];
        <? endif; ?>
        <? if (!empty($optionsExport)): ?>
        <?foreach ($optionsExport as $option):?>
        $helper->Configuration()->saveOption($configId, <?echo var_export($option, 1) ?>);
        <?endforeach;?>
        <? endif; ?>
    }

    public function down()
    {
    //your code ...
    }
}