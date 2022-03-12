<?php
/**
 * Woody Plugin
 * @author      Léo POIROUX
 * @copyright   2022 Raccourci Agency
 */

namespace Woody\Services\Providers;

class Wp
{
    public function searchPages($search = 'undefined', $lang = null)
    {
        global $wpdb;
        $pages = [];

        if (function_exists('pll_current_language') && !is_null($lang)) {
            // If Polylang Pro installed + pages de 1er niveau
            $term_taxonomy_lang_id = $this->getLangTermId($lang);

            if (!empty($term_taxonomy_lang_id)) {
                $sql = "SELECT * FROM wp_posts p JOIN wp_term_relationships t ON p.ID = t.object_id WHERE (p.post_type = 'page' AND p.post_status IN ('publish', 'private', 'draft')) AND p.post_title LIKE '%%%s%%' AND t.term_taxonomy_id = %s ORDER BY p.menu_order ASC";
                $pages = $wpdb->get_results($wpdb->prepare($sql, [$search, $term_taxonomy_lang_id]));
            }
        } else {
            $sql = "SELECT * FROM wp_posts WHERE (post_type = 'page' AND post_status IN ('publish', 'private', 'draft')) AND post_title LIKE '%%%s%%'";
            $pages = $wpdb->get_results($wpdb->prepare($sql, [$search]));
        }

        return $pages;
    }

    public function filterPages($term, $search = null, $theme = null, $place)
    {
        $pages = [];

        $tquery = array(
            'relation'  => 'AND'
        );
        if ($term != 'all') {
            $tax = array(
                'taxonomy'  =>   'page_type',
                'field'     =>   'slug',
                'terms'     =>   $term
            );
            array_push($tquery, $tax);
        }
        if ($theme != 'all') {
            $tax = array(
                'taxonomy'  =>   'themes',
                'field'     =>   'slug',
                'terms'     =>   $theme
            );
            array_push($tquery, $tax);
        }
        if ($place != 'all') {
            $tax = array(
                'taxonomy'  =>   'places',
                'field'     =>   'slug',
                'terms'     =>   $place
            );
            array_push($tquery, $tax);
        }
        $args = array(
            's'         => $search,
            'post_type'     => 'page',
            'post_status'   => array(
                'publish',
                'private',
                'draft',
            ),
            'posts_per_page'=> -1,
            'tax_query'     => $tquery
        );
        $query = new \WP_Query($args);
        $pages = $query->posts;

        return $pages;
    }

    public function getPages($parent_id = 0, $lang = null)
    {
        global $wpdb;
        $pages = [];

        if (function_exists('pll_current_language') && !is_null($lang)) {
            // If Polylang Pro installed + pages de 1er niveau
            $term_taxonomy_lang_id = $this->getLangTermId($lang);

            if (!empty($term_taxonomy_lang_id)) {
                $sql = "SELECT * FROM wp_posts p JOIN wp_term_relationships t ON p.ID = t.object_id WHERE (p.post_type = 'page' AND p.post_status IN ('publish', 'private', 'draft')) AND p.post_parent = %d AND t.term_taxonomy_id = %s ORDER BY p.menu_order ASC";
                $pages = $wpdb->get_results($wpdb->prepare($sql, [$parent_id, $term_taxonomy_lang_id]));
            }
        } else {
            // if no WPML
            $sql = "SELECT * FROM wp_posts p WHERE (post_type = 'page' AND post_status IN ('publish', 'private', 'draft')) AND post_parent = %d ORDER BY menu_order ASC";
            $pages = $wpdb->get_results($wpdb->prepare($sql, [$parent_id]));
        }

        return $pages;
    }

    public function getAttachments($lang = null)
    {
        global $wpdb;
        $attachments = [];

        if (function_exists('pll_current_language') && !is_null($lang)) {
            // If Polylang Pro installed + attachments de 1er niveau
            $term_taxonomy_lang_id = $this->getLangTermId($lang);

            if (!empty($term_taxonomy_lang_id)) {
                $sql = "SELECT * FROM wp_posts p JOIN wp_term_relationships t ON p.ID = t.object_id WHERE p.post_type = 'attachment' AND t.term_taxonomy_id = %s";
                $attachments = $wpdb->get_results($wpdb->prepare($sql, [$term_taxonomy_lang_id]));
            }
        } else {
            // if no WPML
            $sql = "SELECT * FROM wp_posts p WHERE post_type = 'attachment'";
            $attachments = $wpdb->get_results($wpdb->prepare($sql, []));
        }

        return $attachments;
    }

    public function updateChildrenPost($id, $pre_post_update = true)
    {
        $has_children = $this->hasChildren($id);
        if ($has_children) {
            $children_pages = $this->getPages($id);
            if (!empty($children_pages)) {
                foreach ($children_pages as $children_page) {
                    if ($pre_post_update) {
                        do_action('pre_post_update', $children_page->ID, $data = []);
                    } else {
                        do_action('post_updated', $children_page->ID, $children_page, $children_page);
                    }

                    // Recursively
                    $this->updateChildrenPost($children_page->ID, $pre_post_update);
                }
            }
        }
    }

    public function hasChildren($id)
    {
        global $wpdb;
        $sql = "SELECT count(*) FROM wp_posts WHERE (post_type = 'page' AND post_status IN ('publish', 'private', 'draft')) AND post_parent = %d";
        $count = $wpdb->get_var($wpdb->prepare($sql, [$id]));
        return ($count != 0) ? true : false;
    }

    private function getLangTermId($lang = PLL_DEFAULT_LANG)
    {
        if ($lang != false) {
            global $wpdb;
            $term_taxonomy_lang_id = get_transient('woody_polylang-pro_' . $lang);
            if (empty($term_taxonomy_lang_id)) {
                $term_lang = get_term_by('slug', $lang, 'language');
                $sql = "SELECT term_taxonomy_id FROM wp_term_taxonomy WHERE term_id = %d";
                $term_taxonomy_id = $wpdb->get_results($wpdb->prepare($sql, [$term_lang->term_id]));
                if (!empty($term_taxonomy_id[0]) && !empty($term_taxonomy_id[0]->term_taxonomy_id)) {
                    $term_taxonomy_lang_id = $term_taxonomy_id[0]->term_taxonomy_id;
                    set_transient('woody_polylang-pro_' . $lang, $term_taxonomy_id[0]->term_taxonomy_id);
                }
            }

            return $term_taxonomy_lang_id;
        }
    }

    public function human_filesize($bytes, $decimals = 2)
    {
        $factor = floor((strlen($bytes) - 1) / 3);
        if ($factor > 0) {
            $sz = 'KMGT';
        }
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor - 1] . 'B';
    }
}
