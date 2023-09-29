<?php

namespace Advanced_Product\Helper;

use Advanced_Product\AP_Functions;

defined('ADVANCED_PRODUCT') or exit();

require_once( wp_normalize_path(ABSPATH).'wp-load.php');

class AP_Custom_Field_Helper extends BaseHelper {

    public static function get_fields_grouped_taxonomy($taxonomy = 'ap_group_field'){
        $terms = get_terms( array(
            'taxonomy' => $taxonomy,
        ) );

        var_dump($terms);
        die(__METHOD__);
    }

    public static function get_al_taxonomy_by_product_id($product_id,$ap_taxonomy_show){
        $tax_values = array();
        global $wpdb;
        if(!empty($ap_taxonomy_show)){
            foreach ($ap_taxonomy_show as $tax){
                $tax_val = wp_get_object_terms($product_id,$tax);
                if($tax_val){
                    $results = $wpdb->get_row( "SELECT post_title FROM {$wpdb->prefix}posts WHERE post_excerpt = '".$tax_val[0]->taxonomy."'", ARRAY_A );
                    if($results){
                        $tax_values[$results['post_title']] = $tax_val[0]->name;
                    }
                }
            }
        }
        return $tax_values;

    }

    /**
     * Get custom fields in post type ap_custom_field
     * @param array $args An optional exclude of custom fields.
     * @param bool|int $product_id An optional of product.
     * @param string $return An option to function return is WP_Query object or posts array.
     *                       value is: 'posts' or 'query'
     * */
    public static function get_custom_fields($args = array(), $product_id = false, $return = 'posts'){

        // Get custom fields by terms
        $post_args  = array(
            'post_type'     => 'ap_custom_field',
            'post_status'   => 'publish',
            'posts_per_page'    => -1,
            'order'         => 'ASC',
            'orderby'       => 'date',
        );

        if($return == 'query'){
            $post_args['posts_per_page']    = 10;
        }

        if(!empty($args)){
            $post_args  = array_replace_recursive($post_args, $args);
        }

        $store_id   = static::_get_store_id(__METHOD__, $post_args, $product_id);

        if(isset(static::$cache[$store_id])){
            return static::$cache[$store_id];
        }

        $cfields = new \WP_Query($post_args);

        wp_reset_query();
        if(empty($cfields) || is_wp_error($cfields)){
            return false;
        }

        if($return == 'query'){
            return static::$cache[$store_id] = $cfields;
        }

        return static::$cache[$store_id] = $cfields -> get_posts();
    }

    /**
     * Get custom fields in post type ap_custom_field
     * @param array $exclude An optional exclude of custom fields.
     * @param array $include An optional include of custom fields.
     * @param array $options An optional config of custom fields.
     * */
    public static function get_acf_fields($include = array(), $exclude = array(), $options = array()){
        $args = array(
            'post_status' => 'publish',
            'post_type'   => 'ap_custom_field',
            'numberposts' => -1,
        );


        $include_is_number  = false;
        if(!empty($include)){
            $_include   = $include;
            if(is_array($include)){
                foreach($include as $in){
                    if(is_numeric($in)){
                        $include_is_number  = true;
                        break;
                    }
                }
                $_include   = implode(',', $include);
            }else {
                $include_is_number = is_string($include) ? (bool)preg_match('/\d+,*/', $include) : false;
            }
            if($include_is_number) {
                $args['include'] = $_include;
                $args['post_name__in'] = $_include;
            }
        }

        $store_id   = static::_get_store_id(__METHOD__, $include, $exclude, $args, $include_is_number, $options);

        if(isset(static::$cache[$store_id])){
            return static::$cache[$store_id];
        }

        $post_fields = get_posts($args);

        if(!$post_fields){
            return false;
        }

        $fields = array();
        if(!empty($include) && is_string($include) && !$include_is_number){
            $include    = explode(',', $include);
        }

        $exclude_core_field = isset($options['exclude_core_field'])?$options['exclude_core_field']:false;
        foreach($post_fields as $i => $field){
            $acf_f = static::get_custom_field_option_by_id($field -> ID, array(
                'exclude_core_field'    => $exclude_core_field
            ));
            if($acf_f && !empty($acf_f)){
                if(!empty($include) && !$include_is_number && is_array($include) && !in_array($acf_f['_name'], $include)){
                    continue;
                }
                if(!empty($exclude) && isset($acf_f['name']) && in_array($acf_f['name'], $exclude)){
                    continue;
                }

                $index  = array_search($acf_f['name'], $include);
                $fields[$index]   = $acf_f;
            }
        }

        if(!count($fields)){
            return false;
        }

        ksort($fields);

        return static::$cache[$store_id] = $fields;
    }

//    /**
//     * Get custom fields in post type ap_custom_field
//     * @param array $options An optional config of custom fields.
//     * */
//    public static function get_custom_fields_by_product_id($product_id, $options = array()){
//
//        if(!$product_id){
//            return false;
//        }
//
//        $args = array(
//            'post_status' => 'publish',
//            'post_type'   => 'ap_custom_field',
//            'numberposts' => -1,
//        );
//
//        // Get group assigned branch of product
//        $group_fields = static::get_group_fields_by_product($product_id, array(
//            'fields'    => 'ids',
//        ));
//        if(!empty($group_fields)){
//            $args['tax_query']  = array(
//                'taxonomy'  => 'ap_group_field',
//                'terms'     => $group_fields
//            );
//        }
//
//        $store_id   = static::_get_store_id(__METHOD__, $args, $options);
//
//        if(isset(static::$cache[$store_id])){
//            return static::$cache[$store_id];
//        }
//
//        $post_fields = get_posts($args);
//
//        if(!$post_fields){
//            return false;
//        }
//
////        if(is_archive()){
////            var_dump($product_id);
////            var_dump($group_fields);
//////            var_dump($post_fields);
////            die(__FILE__);
////        }
//
//        $fields = array();
//
//        $exclude_core_field = isset($options['exclude_core_field'])?$options['exclude_core_field']:true;
//        foreach($post_fields as $i => $field){
//            $acf_f = static::get_custom_field_option_by_id($field -> ID, array(
//                'exclude_core_field'    => $exclude_core_field
//            ));
//            if(empty($acf_f)){
//                continue;
//            }
//
////            if(is_archive()){
////                var_dump($acf_f);
////                var_dump(__FILE__);
////            }
//            if($acf_f && !empty($acf_f)){
//                $fields[]   = $field;
////                $fields[]   = $acf_f;
//            }
//        }
//
//        if(!count($fields)){
//            return false;
//        }
//
//        ksort($fields);
//
//        return static::$cache[$store_id] = $fields;
//    }

    /**
     * Get custom fields in post type ap_custom_field
     * @param array $options An optional config of custom fields.
     * */
    public static function get_custom_fields_display_flag_by_product_id($flag_name, $product_id, $options = array()){

        if(!$flag_name || !$product_id){
            return false;
        }

        $order      = 'ASC';
        $order_by   = 'date';

        if(function_exists('is_post_type_archive') && is_post_type_archive('ap_product')){
            $archive_order  = \get_field('ap_archive_product_order_by_custom_field', 'option');
            switch ($archive_order){
                default:
                case 'order':
                    $order_by   = 'menu_order';
                    break;
                case 'rorder':
                    $order      = 'DESC';
                    $order_by   = 'menu_order';
                    break;
                case 'date':
                    $order      = 'ASC';
                    $order_by   = 'date';
                    break;
                case 'rdate':
                    $order      = 'DESC';
                    $order_by   = 'date';
                    break;
                case 'alpha':
                    $order      = 'ASC';
                    $order_by   = 'title';
                    break;
                case 'ralpha':
                    $order      = 'DESC';
                    $order_by   = 'title';
                    break;
            }
        }

        $args = array(
            'post_status' => 'publish',
            'post_type'   => 'ap_custom_field',
            'orderby'     => $order_by,
            'order'       => $order,
            'numberposts' => -1,
            'meta_query'  => array(
                array(
                    'key'   => $flag_name,
                    'value' => 1
                )
            ),
            'tax_query' => array(
                array(
                    'taxonomy' => 'ap_group_field',
                    'operator' => 'EXISTS',
                )
            )
        );

        // Get group assigned branch of product
        $group_fields = static::get_group_fields_by_product($product_id, array(
            'fields'    => 'ids',
        ));
        if(!empty($group_fields)){
            $args['tax_query']  = array(
                array(
                    'taxonomy'  => 'ap_group_field',
                    'terms'     => $group_fields
                )
            );
        }

        $store_id   = static::_get_store_id(__METHOD__, $args, $options);

        if(isset(static::$cache[$store_id])){
            return static::$cache[$store_id];
        }

        $post_fields = get_posts($args);

        if(!$post_fields){
            return false;
        }

        $fields = array();

        $exclude_core_field = isset($options['exclude_core_field'])?$options['exclude_core_field']:true;
        foreach($post_fields as $i => $field){
            $acf_f = static::get_custom_field_option_by_id($field -> ID, array(
                'exclude_core_field'    => $exclude_core_field
            ));
            if(empty($acf_f)){
                continue;
            }

            if($acf_f && !empty($acf_f)){
                $fields[]   = $field;
            }
        }

        if(!count($fields)){
            return false;
        }

        ksort($fields);

        return static::$cache[$store_id] = $fields;
    }

    /**
     * Get custom fields without group in post type ap_custom_field
     * @param array $options An optional config of custom fields.
     * */
    public static function get_custom_fields_without_group_display_flag_by_product_id($flag_name, $product_id, $options = array()){

        if(!$flag_name || !$product_id){
            return false;
        }

        $order      = 'ASC';
        $order_by   = 'date';

        if(function_exists('is_post_type_archive') && is_post_type_archive('ap_product')){
            $archive_order  = \get_field('ap_archive_product_order_by_custom_field', 'option');
            switch ($archive_order){
                default:
                case 'order':
                    $order_by   = 'menu_order';
                    break;
                case 'rorder':
                    $order      = 'DESC';
                    $order_by   = 'menu_order';
                    break;
                case 'date':
                    $order      = 'ASC';
                    $order_by   = 'date';
                    break;
                case 'rdate':
                    $order      = 'DESC';
                    $order_by   = 'date';
                    break;
                case 'alpha':
                    $order      = 'ASC';
                    $order_by   = 'title';
                    break;
                case 'ralpha':
                    $order      = 'DESC';
                    $order_by   = 'title';
                    break;
            }
        }

        $args = array(
            'post_status' => 'publish',
            'post_type'   => 'ap_custom_field',
            'orderby'     => $order_by,
            'order'       => $order,
            'numberposts' => -1,
            'meta_query'  => array(
                array(
                    'key'   => $flag_name,
                    'value' => '1'
                )
            ),
            'tax_query' => array(
                array(
                    'taxonomy'  => 'ap_group_field',
                    'operator'  => 'NOT EXISTS'
                )
            )
        );

        $store_id   = static::_get_store_id(__METHOD__, $args, $options);

        if(isset(static::$cache[$store_id])){
            return static::$cache[$store_id];
        }

        $post_fields = get_posts($args);

        if(!$post_fields){
            return false;
        }

        $fields = array();

//        $exclude_core_field = isset($options['exclude_core_field'])?$options['exclude_core_field']:true;
        foreach($post_fields as $i => $field){
            $acf_f = static::get_custom_field_option_by_id($field -> ID);
            if(empty($acf_f)){
                continue;
            }

            if($acf_f && !empty($acf_f)){
                $fields[]   = $field;
            }
        }

        if(!count($fields)){
            return false;
        }

        ksort($fields);

        return static::$cache[$store_id] = $fields;
    }

    /**
     * Get acf fields in post type ap_custom_field
     * @param array $options An optional config of custom fields.
     * */
    public static function get_acf_fields_by_product_id($product_id, $options = array()){

        if(!$product_id){
            return false;
        }

        $args = array(
            'post_status' => 'publish',
            'post_type'   => 'ap_custom_field',
            'numberposts' => -1,
        );

        // Get group assigned branch of product
        $group_fields = static::get_group_fields_by_product($product_id, array(
            'fields'    => 'ids',
        ));
        if(!empty($group_fields)){
            $args['tax_query']  = array(
                'taxonomy'  => 'ap_group_field',
                'terms'     => $group_fields
            );
        }

        $store_id   = static::_get_store_id(__METHOD__, $args, $options);

        if(isset(static::$cache[$store_id])){
            return static::$cache[$store_id];
        }

        $post_fields = get_posts($args);

        if(!$post_fields){
            return false;
        }

        $fields = array();

        $exclude_core_field = isset($options['exclude_core_field'])?$options['exclude_core_field']:false;
        foreach($post_fields as $i => $field){
            $acf_f = static::get_custom_field_option_by_id($field -> ID, array(
                'exclude_core_field'    => $exclude_core_field
            ));
            if($acf_f && !empty($acf_f)){
                $fields[]   = $acf_f;
            }
        }

        if(!count($fields)){
            return false;
        }

        ksort($fields);

        return static::$cache[$store_id] = $fields;
    }

    /**
     * Get acf field attribute by id
     * @param int $field_id An optional of custom field
     * @return array
     * */
    public static function get_custom_field_option_by_id($field_id, $options = array()){

        $store_id   = static::_get_store_id(__METHOD__, $field_id, $options);

        if(isset(static::$cache[$store_id])){
            return static::$cache[$store_id];
        }

        // get acf fields
        $fields = apply_filters('acf/field_group/get_fields', array(), $field_id);

        if(!$fields){
            return array();
        }

        $field          = $fields[0];

        $exclude_core_field = isset($options['exclude_core_field'])?$options['exclude_core_field']:true;

        if($exclude_core_field){
            $exclude_fields = static::get_exclude_fields_registered();

            if(!empty($exclude_fields) && in_array($field['name'], $exclude_fields)){
                return array();
            }
        }

        return static::$cache[$store_id]    = $field;
    }

    /**
     * Get acf field attribute by field name
     * @param string $field_name An optional of field name
     * @return array|bool Acf field attributes created in custom field
     * */
    public static function get_custom_field_option_by_field_name($field_name, $options = array()){

        if(!$field_name){
            return false;
        }

        $store_id   = static::_get_store_id(__METHOD__, $field_name, $options);

        if(isset(static::$cache[$store_id])){
            return static::$cache[$store_id];
        }

        $field_id   = null;
        if($cfield   = static::get_custom_field($field_name)){
            $field_id   = (int) $cfield -> ID;
        }

        if(!$field_id){
            return false;
        }
        $exclude_core_field = isset($options['exclude_core_field'])?$options['exclude_core_field']:false;

        $field  = static::get_custom_field_option_by_id($field_id, array('exclude_core_field' => $exclude_core_field));

        return static::$cache[$store_id]    = $field;
    }

    public static function get_custom_fields_without_protected_field(){
        $args = array(
//            'order'       => 'ASC',
//            'orderby'     => 'ID',
            'orderby' => 'taxonomy, ID', // Just enter 2 parameters here, seprated by comma
            'order'=>'ASC',
//            'orderby'  => array( 'taxonomy' => 'DESC', 'ID' => 'ASC' ),
            'post_status' => 'publish',
            'post_type'   => 'ap_custom_field',
            'numberposts' => -1,
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key'       => '__protected',
                    'value'     => 1,
                    'type'   => 'numeric',
                    'compare'   => '!='
                ),
                array(
                    'key'       => '__protected',
                    'compare'   => 'NOT EXISTS'
                ),
            ),
//            'meta_key'    => 'slug',
//            'meta_value'  =>  $this -> get_taxonomy_name(),
        );

        $store_id   = static::_get_store_id(__METHOD__, $args);

        if(isset(static::$cache[$store_id])){
            return static::$cache[$store_id];
        }

        return static::$cache[$store_id] = $fields = get_posts($args);
    }

    public static function get_custom_fields_have_choices(){
        $fields = static::get_custom_fields();

        $store_id   = static::_get_store_id(__METHOD__, $fields);

        if(isset(static::$cache[$store_id])){
            return static::$cache[$store_id];
        }

        $custom_fields  = array();

        if(!empty($fields)){
            foreach ($fields as $field){
                $acf_f = static::get_custom_field_option_by_id($field -> ID, array('exclude_core_field' => false));
                if($acf_f){
                    if(isset($acf_f['type']) && $acf_f['type'] == 'taxonomy') {
                        $terms = get_terms( array(
                            'taxonomy' => $acf_f['taxonomy'],
                            'hide_empty' => false,
                        ) );
                        if(!isset($acf_f['choices'])){
                            $acf_f['choices']   = array();
                        }else {
                            $acf_f['choices'] = (array)$acf_f['choices'];
                        }
                        if($terms && !empty($terms) && !is_wp_error($terms)){
                            foreach($terms as $term) {
                                $acf_f['choices'][$term -> slug]  = $term -> name;
                            }
                        }
                    }
                    $custom_fields[]    = $acf_f;
                }
            }
        }
        if(!empty($custom_fields)){
            static::$cache[$store_id]   = $custom_fields;
        }

        return $custom_fields;
    }

    public static function is_protected_field($post_id){
        $protected  = get_post_meta($post_id,'__protected', true);
        $protected  = filter_var($protected,  FILTER_VALIDATE_BOOLEAN);

        return $protected;
    }

    public static function get_id_by_post_id($field_name, $post_id){
        if(!$field_name || !$post_id){
            return 0;
        }
        $f_ob = get_field_objects($post_id);

        if(!$f_ob || !isset($f_ob[$field_name])){
            return 0;
        }
        return $f_ob[$field_name]['field_group'];
    }

    public static function get_field_display_flag($flag_name, $field_id){

        if(!$flag_name || !$field_id){
            return false;
        }

        $result = get_post_meta($field_id, $flag_name, true);

        return filter_var($result, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Get field display flag by field name of product
     * @param string $field_name An optional of acf field attribute
     * @param string $flag_name  An optional of product
     * @return bool Show or hide bool
     * */
    public static function get_field_display_flag_by_field_name($flag_name, $field_name){

        if(!$flag_name || !$field_name){
            return false;
        }

        $store_id   = static::_get_store_id(__METHOD__, $flag_name, $field_name);

        if(isset(static::$cache[$store_id])){
            return static::$cache[$store_id];
        }
//        global $wpdb;
//
//        $acf_fields = $wpdb->get_results( $wpdb->prepare( "SELECT p.* FROM $wpdb->posts AS p WHERE p.post_excerpt=%s AND p.post_type=%s"
//            ." LEFT JOIN $wpdb -> postmeta AS pm ON pm.post_id=p.id"
//            , $field_name , 'ap_custom_field' ) );

        $field_id   = 0;
        if($cfield = static::get_custom_field($field_name)){
            $field_id   = $cfield -> ID;
        }

        if(!$field_id){
            return false;
        }

        $result = get_post_meta($field_id, $flag_name, true);

        return filter_var($result, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Get custom fields in post type ap_custom_field
     * @param array $flag_name An optional exclude of custom fields.
     * @param int|WP_Post|null $product   Optional. Post ID or post object. `null`, `false`, `0` and other PHP falsey
     *                                 values return the current global post inside the loop. A numerically valid post
     *                                 ID that points to a non-existent post returns `null`. Defaults to global $post.
     * @param array $args An optional of custom fields's query options.
     * */
    public static function get_fields_by_display_flag($flag_name, $product = null, $args = array()){

        $product_id    = \get_the_ID();
        if(is_numeric($product)){
            $product_id    = $product;
        }elseif(is_object($product)){
            $product_id    = $product -> ID;
        }

        $field_args  = array(
            'meta_key'    => $flag_name,
            'meta_value'  => '1',
        );

        $store_id   = static::_get_store_id(__METHOD__, $flag_name, $field_args, $product_id);

        if(isset(static::$cache[$store_id])){
            return static::$cache[$store_id];
        }


        if($product_id){

            // Get all terms
            $taxonomy   = 'ap_group_field';
            $terms      =  \get_terms( ['taxonomy' => $taxonomy, 'fields' => 'ids'  ] );

            // Get all group fields by product id
            $groups = static::get_group_fields_by_product($product_id, array(
                'fields'    => 'ids',
            ));

            // Filter by group fields
            $field_args['tax_query']    = array(
                'relation'  => 'OR',
                array(
                    'taxonomy'  => 'ap_group_field',
                    'terms'     => $terms,
                    'operator' => 'NOT IN'
//                    'terms'     => \wp_list_pluck($groups, 'term_id')
                ),
                array(
                    'taxonomy'  => 'ap_group_field',
                    'terms'     => $groups
                )
            );
        }

        $fields = static::get_custom_fields($field_args);

        if(!$fields){
            return false;
        }

        return static::$cache[$store_id] = $fields;
    }

    /**
     * Get custom fields in post type ap_custom_field
     * @param array $flag_name An optional flag of custom fields.
     * @param array $exclude An optional exclude of custom fields.
     * */
    public static function get_acf_fields_by_display_flag($flag_name, $exclude = array()){
//        $args = array(
////            'orderby' => 'taxonomy, ID', // Just enter 2 parameters here, seprated by comma
////            'order'=>'ASC',
//            'post_status' => 'publish',
//            'post_type'   => 'ap_custom_field',
//            'numberposts' => -1,
//            'meta_key'    => $flag_name,
//            'meta_value'  => '1',
//        );

        $store_id   = static::_get_store_id(__METHOD__, $flag_name, $exclude);

        if(isset(static::$cache[$store_id])){
            return static::$cache[$store_id];
        }

//        $post_fields = get_posts($args);
        $post_fields = static::get_fields_by_display_flag($flag_name);

        if(!$post_fields){
            return false;
        }

        $fields = array();
        foreach($post_fields as $i => $field){
            $acf_f = static::get_custom_field_option_by_id($field -> ID);
            if($acf_f && !empty($acf_f)){
                if(!empty($exclude) && isset($acf_f['name']) && in_array($acf_f['name'], $exclude)){
                    continue;
                }

                $fields[]   = $acf_f;
            }
        }

        if(empty($fields)){
            return false;
        }

        return static::$cache[$store_id] = $fields;
    }

    public static function get_protected_fields_registered(){
        $protected_fields   = array(
            'ap_branch','ap_category',
            'ap_price',
            /*'ap_price_msrp',*/
            'ap_rental_price',
            'ap_rental_unit',
            'ap_product_type',
            'ap_product_status',
            'ap_time_rental',
            'ap_price_sold',
            'ap_price_contact',
        );

        return array_merge($protected_fields, static::get_protected_fields_media_registered());
    }
    public static function get_protected_fields_media_registered(){
        return array(
            'ap_gallery','ap_video'
        );
    }
    public static function get_exclude_fields_registered(){
        return array(
            'ap_price', 'ap_price_msrp', 'ap_rental_price',
            'ap_rental_unit','ap_price_contact','ap_price_sold','ap_product_type', 'ap_gallery','ap_video'
        );
    }

    public static function get_meta_query_compares(){
        return array('=' => __('=', 'advanced-product'),
            '!=' => __('!=', 'advanced-product'),
            '>' => __('>', 'advanced-product'),
            '>=' => __('>=', 'advanced-product'),
            '<' => __('<', 'advanced-product'),
            '<=' => __('<=', 'advanced-product'),
            'LIKE' => __('LIKE', 'advanced-product'),
            'NOT LIKE' => __('NOT LIKE', 'advanced-product'),
            'IN' => __('IN', 'advanced-product'),
            'NOT IN' => __('NOT IN', 'advanced-product'),
            'BETWEEN' => __('BETWEEN', 'advanced-product'),
            'NOT BETWEEN' => __('NOT BETWEEN', 'advanced-product'),
            'EXISTS' => __('EXISTS', 'advanced-product'),
            'NOT EXISTS' => __('NOT EXISTS', 'advanced-product'),
            'REGEXP' => __('REGEXP', 'advanced-product'),
            'NOT REGEXP' => __('NOT REGEXP', 'advanced-product'),
            'RLIKE' => __('NOT REGEXP', 'advanced-product')
        );
    }

    /**
     * Get field key for field name.
     * Will return first matched acf field key for a give field name.
     *
     * ACF somehow requires a field key, where a sane developer would prefer a human readable field name.
     * http://www.advancedcustomfields.com/resources/update_field/#field_key-vs%20field_name
     *
     * This function will return the field_key of a certain field.
     *
     * @param $field_name String ACF Field name
     * @param $post_id int The post id to check.
     * @return
     */
    public static function acf_get_field_key( $field_name, $post_id = false ) {
        global $wpdb;
        $acf_fields = $wpdb->get_results( $wpdb->prepare( "SELECT ID,post_parent,post_name FROM $wpdb->posts WHERE post_excerpt=%s AND post_type=%s" , $field_name , 'ap_custom_field' ) );

        // get all fields with that name.
        switch ( count( $acf_fields ) ) {
            case 0: // no such field
                return false;
            case 1: // just one result.
                return $acf_fields[0]->post_name;
        }
        // result is ambiguous
        // get IDs of all field groups for this post
        $field_groups_ids = array();
        $field_groups = acf_get_field_groups( array(
            'post_id' => $post_id,
        ) );
        foreach ( $field_groups as $field_group )
            $field_groups_ids[] = $field_group['ID'];

        // Check if field is part of one of the field groups
        // Return the first one.
        foreach ( $acf_fields as $acf_field ) {
            if ( in_array($acf_field->post_parent,$field_groups_ids) )
                return $acf_field->post_name;
        }
        return false;
    }

    /**
     * Get field for field name.
     * Will return first matched acf field key for a give field name.
     *
     * ACF somehow requires a field key, where a sane developer would prefer a human readable field name.
     * http://www.advancedcustomfields.com/resources/update_field/#field_key-vs%20field_name
     *
     * This function will return the field_key of a certain field.
     *
     * @param $field_name String ACF Field name
     * @param $post_id int The post id to check.
     * @return object
     */
    public static function get_custom_field( $field_name, $options = array()) {
        global $wpdb;

        $store_id   = static::_get_store_id(__METHOD__, $field_name, $options);

        if(isset(static::$cache[$store_id])){
            return static::$cache[$store_id];
        }
        $ex_post_id = isset($options['exclude_post_id']) && !empty($options['exclude_post_id'])?$options['exclude_post_id']:0;

        // Get field post with post type ap_custom_field
        $subSql = "SELECT meta_value
                    FROM $wpdb->postmeta
                    WHERE meta_key=%s
                    AND meta_value LIKE %s";
        $sql    = "SELECT DISTINCT p.* FROM $wpdb->posts AS p
                    INNER JOIN $wpdb->postmeta AS pm ON pm.post_id = p.ID
                    INNER JOIN ( $subSql ) AS pmtemp ON pmtemp.meta_value=pm.meta_key
                    WHERE p.post_type=%s";
        if(!empty($ex_post_id)){
            if(is_array($ex_post_id)) {
                $sql .= " AND ID NOT IN(".implode(','. $ex_post_id).")";
            }else{
                $sql    .= " AND ID <> $ex_post_id";
            }
        }
        $sql    = $wpdb -> prepare($sql, '_'.$field_name, 'field_%', 'ap_custom_field');

        if($data = $wpdb -> get_row($sql)){
            return static::$cache[$store_id] = $data;
        }

        $sql    = "SELECT * FROM $wpdb->posts WHERE post_excerpt=%s AND post_type=%s";

        if(!empty($ex_post_id)){
            if(is_array($ex_post_id)) {
                $sql .= " AND ID NOT IN(".implode(','. $ex_post_id).")";
            }else{
                $sql    .= " AND ID <> $ex_post_id";
            }
        }

        $acf_fields = $wpdb->get_results( $wpdb->prepare(  $sql , $field_name , 'ap_custom_field') );

        // Get custom field from post by name in post meta
        if(empty($acf_fields)) {
            $sql        = "SELECT DISTINCT p.* FROM $wpdb->posts AS p"
                . " INNER JOIN $wpdb->postmeta AS pm ON pm.post_id = p.ID"
                . " WHERE pm.meta_key LIKE %s"
                . " AND pm.meta_value LIKE %s"
                . " AND p.post_type=%s";
            if(!empty($ex_post_id)){
                if(is_array($ex_post_id)) {
                    $sql .= " AND p.ID NOT IN(".implode(','. $ex_post_id).")";
                }else{
                    $sql    .= " AND p.ID <> $ex_post_id";
                }
            }
            $acf_fields = $wpdb->get_results($wpdb->prepare( $sql, 'field_%','%s:4:"name";s:'
                .strlen($field_name).':"'.$field_name.'"%', 'ap_custom_field'));
        }


        // get all fields with that name.
        switch ( count( $acf_fields ) ) {
            case 0: // no such field
                return false;
            case 1: // just one result.
                return static::$cache[$store_id] = $acf_fields[0];
        }
        return false;
    }


    /**
     * Get custom fields in post type ap_custom_field
     * @param string|array $tax_slug Slugs of taxonomy.
     * @param array $options An optional config of custom fields.
     * @param array $exclude An optional exclude of custom fields.
     * */
    public static function get_fields_by_group_field_slug($tax_slug, $options = array()){
        $store_id   = static::_get_store_id(__METHOD__, $tax_slug, $options);

        if(empty($tax_slug)){
            return false;
        }

        if(isset(static::$cache[$store_id])){
            return static::$cache[$store_id];
        }

        $_options   = array(
            'tax_query' => array(
                0 => array(
                    'field' => 'slug'
                )
            )
        );
        if(isset($options['field_orderby'])){
            $_options['order']      = $options['field_order'];
            $_options['orderby']    = $options['field_orderby'];
        }
        $fields = static::get_fields_by_group_fields($tax_slug, 'posts', $_options);

        if(!$fields){
            return false;
        }

        return static::$cache[$store_id] = $fields;
    }

    /**
     * Get all fields without group field terms
     * @param  array $options An options of get field query.
     * @return  array|bool of ap_custom_field post type
     * */
    public static function get_fields_without_group_field($options = array()){
        $post_type  = 'ap_custom_field';
        $taxonomy   = 'ap_group_field';
        $terms      =  \get_terms( ['taxonomy' => $taxonomy, 'fields' => 'ids'  ] );

        if(empty($terms) || \is_wp_error($terms)){
            return false;
        }

        $args = [
            'posts_per_page'=> -1,
            'post_type' => $post_type,
            'tax_query' => [
                [
                    'taxonomy' => $taxonomy,
                    'terms'    => $terms,
                    'operator' => 'NOT IN'
                ]
            ],
        ];

        if(is_singular('ap_product')) {
            $order          = 'DESC';
            $order_by       = 'date';
            $field_order    = \get_field('ap_order_by_custom_field', 'option');
            switch ($field_order){
                default:
                case 'order':
                    $order      = 'ASC';
                    $order_by   = 'menu_order';
                    break;
                case 'rorder':
                    $order      = 'DESC';
                    $order_by   = 'menu_order';
                    break;
                case 'date':
                    $order      = 'ASC';
                    $order_by   = 'date';
                    break;
                case 'rdate':
                    $order      = 'DESC';
                    $order_by   = 'date';
                    break;
                case 'alpha':
                    $order      = 'ASC';
                    $order_by   = 'title';
                    break;
                case 'ralpha':
                    $order      = 'DESC';
                    $order_by   = 'title';
                    break;
            }
            $args['order']     = $order;
            $args['orderby']   = $order_by;
        }

        $args   = !empty($options)?array_merge($args, $options):$args;

        $store_id   = static::_get_store_id(__METHOD__, $args, $options);

        if(isset(static::$cache[$store_id])){
            return static::$cache[$store_id];
        }

        $query = new \WP_Query( $args );

        if(empty($query) || \is_wp_error($query)){
            return false;
        }

        $fields = $query -> get_posts();
        wp_reset_query();

        return static::$cache[$store_id] = $fields;

    }

    /**
     * Get acf fields with empty group field taxonomy
     * @return array
     * */
    public static function get_acf_fields_without_group_field($options = array()){
        $cfields    = static::get_fields_without_group_field($options);
        $store_id   = static::_get_store_id(__METHOD__, $cfields, $options);

        if(isset(static::$cache[$store_id])){
            return static::$cache[$store_id];
        }

        if(!$cfields || empty($cfields)){
            return false;
        }

        $fields = array();
        foreach($cfields as $cfield){
            if($acf_field = static::get_custom_field_option_by_id($cfield -> ID)){
                $fields[]   = $acf_field;
            }
        }

        if(empty($fields)){
            return false;
        }

        return static::$cache[$store_id] = $fields;
    }

    /**
     * Get all fields by branch of post
     * @param int|WP_Post|null $product   Optional. Post ID or post object. `null`, `false`, `0` and other PHP falsey
     *                                 values return the current global post inside the loop. A numerically valid post
     *                                 ID that points to a non-existent post returns `null`. Defaults to global $post.
     * @param array $args An optional of group fields's query options.
     * */
    public static function get_group_fields_by_product($product = null, $args = array()){
        $store_id   = static::_get_store_id(__METHOD__, $product, $args);
        if(isset(static::$cache[$store_id])){
            return static::$cache[$store_id];
        }

        $product_id    = get_the_ID();
        if(is_numeric($product)){
            $product_id    = $product;
        }elseif(is_object($product)){
            $product_id    = $product -> ID;
        }

        if(!$product_id){
            return false;
        }

        // Get all branches by post
        $branches = wp_get_post_terms($product_id, 'ap_branch');

        if(empty($branches) || is_wp_error($branches)){
            return false;
        }

        $groups = array();

        // Get all terms
        foreach ($branches as $branch) {
            $_groups    = \get_field('group_field_assigned', 'term_'.$branch -> term_id);

            if(!empty($_groups)) {
                $group_args = array(
                    'taxonomy'  => 'ap_group_field',
                    'order'     => 'DESC'
                );

                if(FieldHelper::term_order_exists()){
                    $group_args['orderby']  = 'term_order';
                    $group_args['order']    = 'ASC';
                }

                $is_number  = true;
                foreach ($_groups as $_group){
                    if(!is_numeric($_group)){
                        $is_number  = false;
                        break;
                    }
                }
                if($is_number){
                    $group_args['include']  = $_groups;
                }else{
                    $group_args['slug']  = $_groups;
                }

                if(isset($args['orderby']) && $args['orderby'] == 'slug__in' && isset($args['slug'])) {
                    $gincludes  = array_intersect($args['slug'], $group_args['slug']);
                    $gothers    = array_diff($group_args['slug'], $args['slug']);

                    $group_args['slug'] = array_merge($gincludes, $gothers);

                    unset($args['slug']);
                }elseif(isset($args['orderby']) && $args['orderby'] == 'include' && isset($args['include'])) {
                    $gincludes  = array_intersect($args['include'], $group_args['include']);
                    $gothers    = array_diff($group_args['include'], $args['include']);

                    $group_args['include'] = array_merge($gincludes, $gothers);

                    unset($args['include']);
                }

                if(!empty($args)) {
                    $group_args = array_replace_recursive($group_args, $args);
                }

                $terms  = get_terms($group_args);
                if(!empty($terms) && !is_wp_error($terms)) {
                    $groups = empty($groups)?$terms:array_replace_recursive($groups, $terms);
                }
            }
        }

        if(empty($groups)){
            return false;
        }

        return static::$cache[$store_id]    = $groups;
    }

    /**
     * Get all fields of terms
     * @param int/string/array $terms Taxonomy term(s)
     * @param string $return An option to function return is WP_Query object or posts array.
     *                       value is: 'posts' or 'query'
     * @return WP_Query|array
     * */
    public static function get_fields_by_group_fields($groups, $return = 'posts', $options = array()){
        $store_id   = static::_get_store_id(__METHOD__, $groups, $return, $options);

        if(isset(static::$cache[$store_id])){
            return static::$cache[$store_id];
        }

        $tax_query  = array(
            0 => array(
                'taxonomy'  => 'ap_group_field',
            )
        );
        if(is_array($groups)){
            $tax_query[0]['terms'] = $groups;
        }elseif(is_object($groups)){
            $tax_query[0]['terms'] = array($groups -> term_id);
        }else{
            $tax_query[0]['terms'] = array($groups);
        }
        // Get custom fields by terms
        $post_args  = array(
            'post_type'     => 'ap_custom_field',
            'post_status'   => 'publish',
            'posts_per_page'   => -1,
            'tax_query'     => $tax_query
        );

        if(is_singular('ap_product')) {
            $order          = 'DESC';
            $order_by       = 'date';
            $field_order    = \get_field('ap_order_by_custom_field', 'option');
            switch ($field_order){
                default:
                case 'order':
                    $order      = 'ASC';
                    $order_by   = 'menu_order';
                    break;
                case 'rorder':
                    $order      = 'DESC';
                    $order_by   = 'menu_order';
                    break;
                case 'date':
                    $order      = 'ASC';
                    $order_by   = 'date';
                    break;
                case 'rdate':
                    $order      = 'DESC';
                    $order_by   = 'date';
                    break;
                case 'alpha':
                    $order      = 'ASC';
                    $order_by   = 'title';
                    break;
                case 'ralpha':
                    $order      = 'DESC';
                    $order_by   = 'title';
                    break;
            }
            $post_args['order']     = $order;
            $post_args['orderby']   = $order_by;
        }


        if(!empty($options)){
            $post_args  = array_replace_recursive($post_args, $options);
        }

        $cfields    = new \WP_Query($post_args);

        if(empty($cfields) || is_wp_error($cfields)){
            return false;
        }

        if($return == 'query'){
            static::$cache[$store_id] = $cfields;
            wp_reset_query();
            return $cfields;
        }else {
            static::$cache[$store_id] = $cfields -> get_posts();
            wp_reset_query();
            return static::$cache[$store_id];
        }
    }

}