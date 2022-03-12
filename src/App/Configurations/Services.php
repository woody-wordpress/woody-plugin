<?php
/**
 * Woody Plugin
 * @author      Léo POIROUX
 * @copyright   2022 Raccourci Agency
 */

namespace Woody\App\Configurations;

class Services
{
    private static $definitions;

    private static function definitions()
    {
        return [
            'woody.wp'     => [
                'class'     => \Woody\Services\Providers\Wp::class,
            ],
            'guzzle'             => [
                'class' => \GuzzleHttp\Client::class,
            ],
            'finder'             => [
                'class' => \Symfony\Component\Finder\Finder::class // unused
            ],
            'handler.wp'         => [
                'class'     => \Woody\Services\Handlers\WP::class, // unused
                'arguments' => [
                    ['service' => 'manager.parameters']
                ]
            ],
            'manager.parameters' => [
                'class' => \Woody\Services\ParameterManager::class,
            ]
        ];
    }

    public static function getDefinition($key, $subKey = null)
    {
        self::loadDefinitions();
        $definition = self::$definitions[$key];
        return null === $subKey ? $definition : $definition[$subKey];
    }

    public static function getDefinitions()
    {
        return self::$definitions;
    }

    public static function loadDefinitions()
    {
        if (empty(self::$definitions)) {
            self::$definitions = self::definitions();
        }
    }

    public static function addDefinitions($definitions)
    {
        self::loadDefinitions();
        self::$definitions = array_merge(self::$definitions, $definitions);
    }
}
