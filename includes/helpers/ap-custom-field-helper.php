<?php

namespace Advanced_Product\Helper;

use Advanced_Product\AP_Functions;

defined('ADVANCED_PRODUCT') or exit();

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
     * */
    public static function get_custom_fields($exclude = array()){
        $args = array(
//            'order'       => 'ASC',
//            'orderby'     => 'ID',
//            'orderby' => 'taxonomy, ID', // Just enter 2 parameters here, seprated by comma
//            'orderby' => 'taxonomy, name', // Just enter 2 parameters here, seprated by comma
//            'order'=>'ASC',
//            'orderby'  => array( 'taxonomy' => 'DESC', 'ID' => 'ASC' ),
            'orderby'  => array( 'taxonomy' => 'DESC', 'ID' => 'ASC' ),
            'post_status' => 'publish',
            'post_type'   => 'ap_custom_field',
            'numberposts' => -1,
            'tax_query'   => array(
                'taxonomy' => 'ap_group_field',
            ),

//            'meta_key'    => 'slug',
//            'meta_value'  =>  $this -> get_taxonomy_name(),
        );

        $store_id   = static::_get_store_id(__METHOD__, $exclude, $args);

        if(isset(static::$cache[$store_id])){
            return static::$cache[$store_id];
        }

        $fields = get_posts($args);

        if(!$fields){
            return false;
        }

        if(!empty($exclude)){
            foreach($fields as $i => $field){
                $acf_f = AP_Custom_Field_Helper::get_custom_field_option_by_id($field -> ID);
                if(isset($acf_f['name']) && in_array($acf_f['name'], $exclude)){
                    unset($fields[$i]);
                }
            }
        }

        return static::$cache[$store_id] = $fields;
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

        return static::$cache[$store_id]    = $fields[0];
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
                if($acf_f['type'] == 'taxonomy') {
                    $terms = get_terms( array(
                        'taxonomy' => $acf_f['taxonomy'],
                        'hide_empty' => false,
                    ) );
                    if($terms && !empty($terms)){
                        foreach($terms as $term) {
                            $acf_f['choices'][$term -> term_id]  = $term -> name;
                        }
                    }
                }
                if(isset($acf_f['choices']) || $acf_f['type'] == 'taxonomy'){
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
     * */
    public static function get_fields_by_display_flag($flag_name){
        $args = array(
//            'order'       => 'ASC',
//            'orderby'     => 'ID',
            'orderby' => 'taxonomy, ID', // Just enter 2 parameters here, seprated by comma
            'order'=>'ASC',
//            'orderby'  => array( 'taxonomy' => 'DESC', 'ID' => 'ASC' ),
            'post_status' => 'publish',
            'post_type'   => 'ap_custom_field',
            'numberposts' => -1,
            'meta_key'    => $flag_name,
            'meta_value'  => '1',
        );

        $store_id   = static::_get_store_id(__METHOD__, $flag_name, $args);

        if(isset(static::$cache[$store_id])){
            return static::$cache[$store_id];
        }

        $fields = get_posts($args);

        if(!$fields){
            return false;
        }

//        if(!empty($exclude)){
//            foreach($fields as $i => $field){
//                $acf_f = AP_Custom_Field_Helper::get_custom_field_option_by_id($field -> ID);
//                if(isset($acf_f['name']) && in_array($acf_f['name'], $exclude)){
//                    unset($fields[$i]);
//                }
//            }
//        }

        return static::$cache[$store_id] = $fields;
    }

    /**
     * Get custom fields in post type ap_custom_field
     * @param array $flag_name An optional exclude of custom fields.
     * */
    public static function get_acf_fields_by_display_flag($flag_name){
        $args = array(
//            'order'       => 'ASC',
//            'orderby'     => 'ID',
            'orderby' => 'taxonomy, ID', // Just enter 2 parameters here, seprated by comma
            'order'=>'ASC',
//            'orderby'  => array( 'taxonomy' => 'DESC', 'ID' => 'ASC' ),
            'post_status' => 'publish',
            'post_type'   => 'ap_custom_field',
            'numberposts' => -1,
            'meta_key'    => $flag_name,
            'meta_value'  => '1',
        );

        $store_id   = static::_get_store_id(__METHOD__, $flag_name, $args);

        if(isset(static::$cache[$store_id])){
            return static::$cache[$store_id];
        }

        $post_fields = get_posts($args);

        if(!$post_fields){
            return false;
        }

        $fields = array();
//        if(!empty($include) && is_string($include) && !$include_is_number){
//            $include    = explode(',', $include);
//        }
        foreach($post_fields as $i => $field){
            $acf_f = AP_Custom_Field_Helper::get_custom_field_option_by_id($field -> ID);
            if($acf_f && !empty($acf_f)){
//                if(!empty($include) && !$include_is_number && is_array($include) && !in_array($acf_f['_name'], $include)){
//                    continue;
//                }
//                if(!empty($exclude) && isset($acf_f['name']) && in_array($acf_f['name'], $exclude)){
//                    continue;
//                }

                $fields[]   = $acf_f;
            }
        }

        return static::$cache[$store_id] = $fields;
    }
    
    public static function get_protected_fields_registered(){
        return array(
            'ap_branch','ap_category',
            'ap_price',
            /*'ap_price_msrp',
            'ap_price_rental',*/
            'ap_product_status',
            'ap_gallery','ap_video','ap_time_rental',
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
}