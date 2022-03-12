<?php
/**
 * Woody Plugin
 * @author      Léo POIROUX
 * @copyright   2022 Raccourci Agency
 */

namespace Woody\Modules;

use Woody\App\Container;
use Woody\Services\ParameterManager;

interface ModuleInterface
{
    public function initialize(ParameterManager $parameters, Container $container);
    public function getKey();
    public function registerCommands();
    public function getMenu();
    public function subscribeHooks();
    public function activate();
    public function deactivate();
    public static function dependencyParameters($env);
    public static function dependencyServiceDefinitions();
}
