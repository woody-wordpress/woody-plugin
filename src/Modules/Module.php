<?php
/**
 * Woody Plugin
 * @author      Léo POIROUX
 * @copyright   2022 Raccourci Agency
 */

namespace Woody\Modules;

use Woody\App\Container;
use Woody\Services\ParameterManager;

abstract class Module implements ModuleInterface
{
    protected $parameters;
    /**
     * @var Container
     */
    protected $container;
    protected static $currentPath;
    protected static $moduleName;
    protected static $key;

    /**
     * @param $serviceKey
     * @return mixed
     */
    protected function get($serviceKey)
    {
        return $this->container->get($serviceKey);
    }

    public function initialize(ParameterManager $parameters, Container $container)
    {
        $this->parameters = $parameters;
        $this->container = $container;
    }

    public function registerRoutes()
    {
        // To implement if you have something to do in the plugin activation
    }

    public function getKey()
    {
        $prefix = 'woody_';
        if (strpos(static::$key, $prefix) !== 0) {
            throw new \Exception('key "' . static::$key . '" must be prefixed with ' . $prefix);
        }
        return static::$key;
    }

    public function subscribeHooks()
    {
        throw new \Exception('subscribeHooks must be implemented');
    }

    public function registerCommands()
    {
        // To implement if you have something to do in the plugin activation
    }

    public function getMenu()
    {
        // To implement if you have something to do in the plugin activation
    }

    public function activate()
    {
        // To implement if you have something to do in the plugin activation
    }

    public function deactivate()
    {
        // To implement if you have something to do in the plugin deactivation
    }

    public static function getModuleName()
    {
        self::getCurrentPath();
        if (self::$currentPath && !self::$moduleName) {
            self::$moduleName = substr(self::$currentPath, strrpos(self::$currentPath, DIRECTORY_SEPARATOR) + 1);
        }
        return self::$moduleName;
    }

    public static function getCurrentPath()
    {
        if (!self::$currentPath) {
            $reflection = new \ReflectionClass(get_called_class());
            self::$currentPath = dirname($reflection->getFileName());
        }
        return self::$currentPath;
    }

    public function renderTemplate($name)
    {
        include $this->parameters->get('dir.modules.resources.admin.template', [
            $this::getModuleName(),
            DIRECTORY_SEPARATOR . $name . '.php'
        ]);
    }

    public static function dependencyParameters($env)
    {
        return [];
    }

    public static function dependencyServiceDefinitions()
    {
        return [];
    }

    public function assetPath($filename)
    {
        $manifest = [];
        $manifest_path = __DIR__ . '/../../dist/rev-manifest.json';
        if (file_exists($manifest_path)) {
            $manifest = json_decode(file_get_contents($manifest_path), true);

            if (!empty($manifest[$filename])) {
                $filename = $manifest[$filename];
            }
        }

        return plugins_url() . '/woody-plugin/dist/' . $filename;
    }

    public function addonAssetPath($addon, $filename)
    {
        $manifest = [];
        $manifest_path = WP_DIST_DIR . '/addons/' . $addon .'/rev-manifest.json';
        if (file_exists($manifest_path)) {
            $manifest = json_decode(file_get_contents($manifest_path), true);

            if (!empty($manifest[$filename])) {
                $filename = $manifest[$filename];
            }
        }

        return WP_DIST_URL . '/addons/' . $addon .'/' . $filename;
    }
}
