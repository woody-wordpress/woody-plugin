<?php
/**
 * Woody Plugin
 * @author      Léo POIROUX
 * @copyright   2022 Raccourci Agency
 */

namespace Woody\App\Configurations;

use Woody\Modules\ModuleInterface;

class Modules
{
    public static $moduleInstances;

    /**
     * @return ModuleInterface[]
     */
    public static function load()
    {
        self::$moduleInstances = [
            'plugin'     => new \Woody\Modules\Plugin\Plugin(),
            'taxonomies' => new \Woody\Modules\Plugin\Addons\Taxonomies\Taxonomies(),
        ];

        /* --------------------------------------
        LIBRARIES
        -------------------------------------- */
        if (class_exists('Woody\Lib\DropZone\DropZone')) {
            self::$moduleInstances['lib_dropzone'] = new \Woody\Lib\DropZone\DropZone();
        }

        if (class_exists('Woody\Lib\Polylang\Polylang')) {
            self::$moduleInstances['lib_polylang'] = new \Woody\Lib\Polylang\Polylang();
        }

        if (class_exists('Woody\Lib\Varnish\Varnish')) {
            self::$moduleInstances['lib_varnish'] = new \Woody\Lib\Varnish\Varnish();
        }

        return self::$moduleInstances;
    }

    public static function get($moduleName)
    {
        return empty(self::$moduleInstances[$moduleName]) ? null : self::$moduleInstances[$moduleName];
    }
}
