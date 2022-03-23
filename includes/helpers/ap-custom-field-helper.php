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

    /**
     * Get custom fields in post type ap_custom_field
     * @param array $exclude An optional exclude of custom fields.
     * @param string $return An option to function return is WP_Query object or posts array.
     *                       value is: 'posts' or 'query'
     * */
    public static function get_custom_fields($args = array(), $return = 'posts'){

        // Get custom fields by terms
        $post_args  = array(
            'post_type'     => 'ap_custom_field',
            'post_status'   => 'publish',
            'posts_per_page'   => -1,
        );

        if($return == 'query'){
            $post_args['posts_per_page']    = 10;
        }

        if(!empty($args)){
            $post_args  = array_replace_recursive($post_args, $args);
        }

        $store_id   = static::_get_store_id(__METHOD__, $post_args);

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
     * */
    public static function get_acf_fields($include = array(), $exclude = array()){
        $args = array(
//            'order'       => 'ASC',
//            'orderby'     => 'ID',
//            'orderby' => 'taxonomy, ID', // Just enter 2 parameters here, seprated by comma
//            'orderby' => 'taxonomy, name', // Just enter 2 parameters here, seprated by comma
//            'order'=>'ASC',
//            'orderby'  => array( 'taxonomy' => 'DESC', 'ID' => 'ASC' ),
//            'orderby'  => array( 'taxonomy' => 'DESC', 'ID' => 'ASC' ),
            'post_status' => 'publish',
            'post_type'   => 'ap_custom_field',
            'numberposts' => -1,
//            'tax_query'   => array(
//                'taxonomy' => 'ap_group_field',
//            ),

//            'meta_key'    => 'slug',
//            'meta_value'  =>  $this -> get_taxonomy_name(),
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

        $store_id   = static::_get_store_id(__METHOD__, $include, $exclude, $args, $include_is_number);

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
        foreach($post_fields as $i => $field){
            $acf_f = AP_Custom_Field_Helper::get_custom_field_option_by_id($field -> ID);
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
        ksort($fields);
////        var_dump(ksort($fields));
//        var_dump($fields);
//        die(__FILE__);

        if(!count($fields)){
            return false;
        }

        return static::$cache[$store_id] = $fields;
    }

    public static function get_custom_field_option_by_id($post_id){

        $store_id   = static::_get_store_id(__METHOD__, $post_id);

        if(isset(static::$cache[$store_id])){
            return static::$cache[$store_id];
        }

        // get acf fields
        $fields = apply_filters('acf/field_group/get_fields', array(), $post_id);

        if(!$fields){
            return array();
        }

        $field          = $fields[0];
        $exclude_fields = static::get_exclude_fields_registered();

        if(!empty($exclude_fields) && in_array($field['name'], $exclude_fields)){
            return array();
        }

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
                $acf_f = AP_Custom_Field_Helper::get_custom_field_option_by_id($field -> ID);
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
//                    var_dump($acf_f);
//                    var_dump($field);
//                    die(__FILE__);
//                    if(isset($acf_f['type']) && $acf_f['type'] == 'taxonomy') {
//                        $terms = get_terms( array(
//                            'taxonomy' => $acf_f['taxonomy'],
//                            'hide_empty' => false,
//                        ) );
//                        if($terms && !empty($terms)){
//                            if(!isset($acf_f['choices'])){
//                                $acf_f['choices']   = array();
//                            }else {
//                                $acf_f['choices'] = is_array($acf_f['choices']) ? $acf_f['choices'] : (array)$acf_f['choices'];
//                            }
//                            foreach($terms as $term) {
//                                $acf_f['choices'][$term -> term_id]  = $term -> name;
//                            }
//                        }
//                    }
//                    if(isset($acf_f['choices']) || (isset($acf_f['type']) && $acf_f['type'] == 'taxonomy')){
//                        $custom_fields[]    = $acf_f;
//                    }
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
//        if(empty($))
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
            $acf_f = AP_Custom_Field_Helper::get_custom_field_option_by_id($field -> ID);
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
            /*'ap_price_msrp',
            'ap_price_rental',*/
            'ap_product_status',
            'ap_time_rental',
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
            'ap_price', 'ap_price_msrp', 'ap_gallery','ap_video'
        );
    }

    public static function get_meta_query_compares(){
        return array('=' => __('=', AP_Functions::get_my_text_domain()),
            '!=' => __('!=', AP_Functions::get_my_text_domain()),
            '>' => __('>', AP_Functions::get_my_text_domain()),
            '>=' => __('>=', AP_Functions::get_my_text_domain()),
            '<' => __('<', AP_Functions::get_my_text_domain()),
            '<=' => __('<=', AP_Functions::get_my_text_domain()),
            'LIKE' => __('LIKE', AP_Functions::get_my_text_domain()),
            'NOT LIKE' => __('NOT LIKE', AP_Functions::get_my_text_domain()),
            'IN' => __('IN', AP_Functions::get_my_text_domain()),
            'NOT IN' => __('NOT IN', AP_Functions::get_my_text_domain()),
            'BETWEEN' => __('BETWEEN', AP_Functions::get_my_text_domain()),
            'NOT BETWEEN' => __('NOT BETWEEN', AP_Functions::get_my_text_domain()),
            'EXISTS' => __('EXISTS', AP_Functions::get_my_text_domain()),
            'NOT EXISTS' => __('NOT EXISTS', AP_Functions::get_my_text_domain()),
            'REGEXP' => __('REGEXP', AP_Functions::get_my_text_domain()),
            'NOT REGEXP' => __('NOT REGEXP', AP_Functions::get_my_text_domain()),
            'RLIKE' => __('NOT REGEXP', AP_Functions::get_my_text_domain())
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
     * @return
     */
    public static function get_custom_field( $field_name) {
        global $wpdb;

        $store_id   = static::_get_store_id(__METHOD__, $field_name);

        if(isset(static::$cache[$store_id])){
            return static::$cache[$store_id];
        }

        $acf_fields = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_excerpt=%s AND post_type=%s" , $field_name , 'ap_custom_field' ) );


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

        $fields = static::get_fields_by_group_fields($tax_slug, 'posts', array(
            'tax_query' => array(
                0 => array(
                    'field' => 'slug'
                )
            )
        ));

//        var_dump($tax_slug);
//        var_dump($fields);
//        die(__FILE__);


//        global $wpdb;
//        $sql    = 'SELECT wp.*, wt.name AS term_name, wt.slug AS term_slug FROM '.$wpdb -> posts.' AS wp';
//        $sql   .= ' LEFT JOIN '.$wpdb -> term_relationships.' wtr ON (wp.ID = wtr.object_id OR wtr.object_id IS NULL)';
//        $sql   .= ' LEFT JOIN '.$wpdb -> term_taxonomy.' wtt ON (wtr.term_taxonomy_id = wtt.term_taxonomy_id AND wtt.taxonomy="ap_group_field")';
//        $sql   .= ' LEFT JOIN '.$wpdb -> terms.' wt ON (wt.term_id = wtt.term_id)';
//        $sql   .= ' WHERE post_type="ap_custom_field"';
//        $sql   .= ' AND wp.post_status="publish"';
//
//        if(is_array($tax_slug)) {
//            $sql   .= ' AND wt.slug IN("'.implode('","', $tax_slug).'")';
//        }else{
//            $sql   .= ' AND wt.slug = "'.$tax_slug.'"';
//        }
//
//        $sql   .= ' GROUP BY wp.id';
//        $sql   .= ' ORDER BY wt.term_id ASC, wp.id DESC';
//
//        $fields = $wpdb -> get_results($sql);

        if(!$fields){
            return false;
        }

        return static::$cache[$store_id] = $fields;
    }

    /**
     * Get all fields without group field terms
     * @param  array $options An options of get field query.
     * @return  fields of ap_custom_field post type
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

        $args   = !empty($options)?array_merge($args, $options):$args;

        $store_id   = static::_get_store_id(__METHOD__, $args, $options);

        if(isset(static::$cache[$store_id])){
            return static::$cache[$store_id];
        }

        $query = new \WP_Query( $args );

//        var_dump(empty($terms) || \is_wp_error($terms));

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
        $store_id   = static::_get_store_id(__METHOD__, $product);
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

//        // Get all terms
//        $taxonomy   = 'ap_group_field';
//        $terms      =  \get_terms( ['taxonomy' => $taxonomy, 'fields' => 'ids'  ] );

        foreach ($branches as $branch) {
            $_groups    = get_option($branch -> taxonomy.'_'.$branch -> term_id.'_group_field_assigned');

            if(!empty($_groups)) {
                $group_args = array(
                    'taxonomy' => 'ap_group_field',
                    'include' => $_groups
                );
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
            'posts_per_page '   => -1,
            'tax_query'     => $tax_query
//            'tax_query'     => array(
//                array(
//                    'taxonomy'  => 'ap_group_field',
//                    'field' => 'term_id',
//                    'terms' => array($terms -> term_id)
//                )
//            )
        );

        if($return == 'query'){
            $post_args['posts_per_page']    = 10;
        }

        if(!empty($options)){
            $post_args  = array_replace_recursive($post_args, $options);
        }

        $cfields    = new \WP_Query($post_args);

        wp_reset_query();
        if(empty($cfields) || is_wp_error($cfields)){
            return false;
        }

        if($return == 'query'){
            return static::$cache[$store_id] = $cfields;
        }else {
            return static::$cache[$store_id] = $cfields -> get_posts();
        }
    }

}