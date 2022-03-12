<?php

/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 27/02/18
 * Time: 17:29
 */

namespace Woody\Modules\Plugin;

use Woody\App\Container;
use Woody\Modules\Module;
use Woody\Services\ParameterManager;
use League\CommonMark\CommonMarkConverter;

final class Plugin extends Module
{
    protected static $key = 'woody_plugin';
    protected $twig;

    public function initialize(ParameterManager $parameters, Container $container)
    {
        define('WOODY_PLUGIN_ROOT', __FILE__);
        define('WOODY_PLUGIN_DIR_ROOT', dirname(WOODY_PLUGIN_ROOT));

        parent::initialize($parameters, $container);
    }

    public function subscribeHooks()
    {
        register_activation_hook(PLUGIN_WOODY_ROOT, [$this, 'activate']);
        register_deactivation_hook(PLUGIN_WOODY_ROOT, [$this, 'deactivate']);

        add_filter('woody_theme_siteconfig', [$this, 'woodyThemeSiteconfig']);
    }

    public function woodyThemeSiteconfig($siteConfig)
    {
        $siteConfig['site_key'] = WP_SITE_KEY;

        return $siteConfig;
    }
}
