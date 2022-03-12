<?php
/**
 * Woody Taxonomies
 * @author      Thomas Navarro
 * @copyright   2019 Raccourci Agency
 */

namespace Woody\Modules\Plugin\Addons\Taxonomies;

use Woody\App\Container;
use Woody\Modules\Module;
use Woody\Services\ParameterManager;
use Symfony\Component\Finder\Finder;

final class Taxonomies extends Module
{
    protected static $key = 'woody_taxonomies';

    public function initialize(ParameterManager $parameters, Container $container)
    {
        define('WOODY_TAXONOMIES_VERSION', '1.0');
        define('WOODY_TAXONOMIES_ROOT', __FILE__);
        define('WOODY_TAXONOMIES_DIR_ROOT', dirname(WOODY_TAXONOMIES_ROOT));

        parent::initialize($parameters, $container);
    }

    public function subscribeHooks()
    {
        register_activation_hook(WOODY_TAXONOMIES_ROOT, [$this, 'activate']);
        register_deactivation_hook(WOODY_TAXONOMIES_ROOT, [$this, 'deactivate']);

        add_filter('acf/settings/load_json', [$this, 'acfJsonLoad']);
        if (WP_ENV == 'dev') {
            add_filter('woody_acf_save_paths', [$this, 'acfJsonSave']);
        }

        // Permet de lier un woody_icon à un terme
        add_filter('woody_taxonomies_with_icons', [$this, 'setTermsWithIcons'], 10, 1);

        // Ajoute un switch vrai/faux pour afficher ou non l'icône lié au terme sur les Hero
        add_action('acf/init', [$this, 'addTermIconField'], 11);
    }

    public function acfJsonLoad($paths)
    {
        $paths[] = __DIR__ . '/Resources/ACF';
        return $paths;
    }

    public function acfJsonSave($groups)
    {
        $acf_json_path = __DIR__ . '/Resources/ACF';

        $finder = new Finder();
        $finder->files()->in($acf_json_path)->name('*.json');
        foreach ($finder as $file) {
            $filename = str_replace('.json', '', $file->getRelativePathname());
            $groups[$filename] = $acf_json_path;
        }

        return $groups;
    }

    public function setTermsWithIcons($terms)
    {
        if (!empty($terms)) {
            foreach ($terms as $term) {
                $term->term_icon = get_term_meta($term->term_id, 'woody_icon', true);
            }
        }

        return $terms;
    }

    public function addTermIconField()
    {
        $termIconField = [
            'key' => 'field_5d1b721b0789b',
            'label' => 'Afficher l\'icône associée',
            'name' => 'page_heading_term_icon',
            'instructions' => 'Affiche l\'icône qui est liée au terme si celui-ci est coché',
            'type' => 'true_false',
            'parent' => 'group_5b052bbee40a4',
            "default_value" => 0,
            "ui" => 1,
        ];

        acf_add_local_field($termIconField);
    }
}
