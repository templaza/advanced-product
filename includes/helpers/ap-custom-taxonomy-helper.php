<?php

namespace Advanced_Product\Helper;

use Advanced_Product\AP_Functions;

defined('ADVANCED_PRODUCT') or exit();

class AP_Custom_Taxonomy_Helper extends BaseHelper{

    public static function get_taxonomies(){

        $store_id   = static::_get_store_id(__METHOD__, func_get_args());

        if(isset(static::$cache[$store_id])){
            return static::$cache[$store_id];
        }

        $args = array(
            'order'       => 'ASC',
            'orderby'     => 'ID',
            'post_status' => 'publish',
            'post_type'   => 'ap_custom_category'
        );

        $categories = get_posts( $args );
        wp_reset_postdata();

        if(!empty($categories)){
            return static::$cache[$store_id]    = $categories;
        }

        return false;
    }

    // get taxonomy image url for the given term_id (Place holder image by default)
    public static function get_image_url($term_id = NULL, $size = 'full', $return_placeholder = FALSE) {

        $store_id   = static::_get_store_id(__METHOD__, func_get_args());

        if(isset(static::$cache[$store_id])){
            return static::$cache[$store_id];
        }

        if (!$term_id) {
            if (is_category())
                $term_id = get_query_var('cat');
            elseif (is_tax()) {
                $current_term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
                $term_id = $current_term->term_id;
            }
        }

        $taxonomy_image_url = get_option('car_dealer_taxonomy_image'.$term_id);
        if(!empty($taxonomy_image_url)) {
            $attachment_id = car_dealer_get_attachment_id_by_url($taxonomy_image_url);
            if(!empty($attachment_id)) {
                $taxonomy_image_url = wp_get_attachment_image_src($attachment_id, $size);
                $taxonomy_image_url = $taxonomy_image_url[0];
            }
        }

        if ($return_placeholder) {
            $taxonomy_image_url = !empty($taxonomy_image_url) ? $taxonomy_image_url : AP_Functions::get_my_url() . '/assets/images/no_image.png';
        }


        if(!empty($taxonomy_image_url)) {
            static::$cache[$store_id]   = $taxonomy_image_url;
        }
        return $taxonomy_image_url;
    }
}