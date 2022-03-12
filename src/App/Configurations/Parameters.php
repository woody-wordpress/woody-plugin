<?php
/**
 * Woody Plugin
 * @author      Léo POIROUX
 * @copyright   2022 Raccourci Agency
 */

namespace Woody\App\Configurations;

class Parameters
{
    private static function init()
    {
        return [
            'default'  => [
                'dir'         => [
                    'resources' => [
                        'admin' => [
                            'template' => PLUGIN_WOODY_DIR_ROOT . '/src/Resources/Admin/Templates'
                        ]
                    ],
                    'modules'   => [
                        'resources' => [
                            'languages' => PLUGIN_WOODY_DIR_ROOT . '/src/Modules/%s/Resources/Languages',
                            'admin'     => [
                                'templates' => PLUGIN_WOODY_DIR_ROOT . '/src/Modules/%s/Resources/Admin/Templates'
                            ]
                        ]
                    ]
                ],
            ],
            'dev' => [
                'environment' => 'DEV',
            ],
            'preprod' => [
                'environment' => 'PREPROD',
            ],
            'prod' => [
                'environment' => 'PROD'
            ]
        ];
    }

    private static $parameters;
    private static $env;

    public static function setEnvironment($env)
    {
        self::$env = $env;
    }

    public static function load()
    {
        if (!self::$parameters) {
            $parameters = self::init();
            self::$parameters = $parameters['default'];

            if (!empty($parameters[self::$env])) {
                self::$parameters = array_replace_recursive(self::$parameters, $parameters[self::$env]);
            }
        }

        return self::$parameters;
    }

    public static function addParameters($parameters)
    {
        if (!empty($parameters)) {
            self::load();
            self::$parameters = array_merge(self::$parameters, $parameters);
        }
    }

    public static function getAll()
    {
        self::load();
        return self::$parameters;
    }
}
