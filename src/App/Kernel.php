<?php
/**
 * Woody Plugin
 * @author      Léo POIROUX
 * @copyright   2022 Raccourci Agency
 */

namespace Woody\App;

use Woody\App\Configurations;
use Woody\Modules\ModuleInterface;

class Kernel
{
    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $pluginName The string used to uniquely identify this plugin.
     */
    public const pluginName = 'woody';

    /**
     * @var Container
     */
    private $container;

    /**
     * @var ModuleInterface[]
     */
    private $modules;

    private $parametersConfiguration;

    public function __construct($env)
    {
        Configurations\Parameters::setEnvironment($env);
        foreach (Configurations\Modules::load() as $module) {
            if (isset($this->modules[$module->getKey()])) {
                throw new \Exception('The module key "' . $module->getKey() . '" must be unique');
            }
            $this->modules[$module->getKey()] = $module;

            Configurations\Services::addDefinitions($module::dependencyServiceDefinitions());
            Configurations\Parameters::addParameters($module::dependencyParameters($env));
        }

        $serviceDefinitions = Configurations\Services::getDefinitions();
        $this->container = new Container($serviceDefinitions);
        $this->parametersConfiguration = $this->container->get('manager.parameters')->setParameters(Configurations\Parameters::getAll());

        foreach ($this->modules as $module) {
            $module->initialize($this->parametersConfiguration, $this->container);
            $module->subscribeHooks();
        }

        if (defined('WP_CLI') && \WP_CLI) {
            foreach ($this->modules as $module) {
                $module->registerCommands();
            }
        }
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function getPluginName()
    {
        return self::pluginName;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return mixed
     */
    public function getModules()
    {
        return $this->modules;
    }

    public function getModule($moduleKey)
    {
        return isset($this->modules[$moduleKey]) ? $this->modules[$moduleKey] : null;
    }
}
