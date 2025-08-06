<?php

namespace Advanced_Product\Helper;

use Advanced_Product\AP_Functions;

defined('ADVANCED_PRODUCT') or exit();

/**
 * This class often uses in back-end
 * */
class FieldHelper extends BaseHelper {
//    protected static $cache    = array();

    protected static $fields    = array();

    /*
     * Get fields filter by location rules
     * Apply to acf v4
    */
    public static function get_fields_by_location_rules($filter = array()){

        $store_id   = static::_get_store_id(__METHOD__, $filter);

        if(isset(static::$cache[$store_id])){
            return static::$cache[$store_id];
        }

//        $filter = array(
//            'post_type' => get_post_type()
//        );
        $fields     = array();
        $group_ids  = array();
        $group_ids  = apply_filters( 'acf/location/match_field_groups', $group_ids, $filter );
        $acfs       = apply_filters('acf/get_field_groups', array());

        if( $acfs )
        {
            foreach( $acfs as $acf )
            {
                // load options
                $acf['options'] = apply_filters('acf/field_group/get_options', array(), $acf['id']);
                if(!isset($acf['options']['layout'])){
                    $acf['options']['layout']   = '';
                }

                // vars
                $show = in_array( $acf['id'], $group_ids ) ? true : false;

                if( !$show )
                {
                    continue;
                }

                $_fields    = apply_filters('acf/field_group/get_fields', array(), $acf['id']);
                $fields     = array_merge($fields, $_fields);
            }
        }
        return $fields;
    }

    /* Get fields of a group */
    public static function get_fields_by_group($group, $filter = array()){
        $fields = static::get_fields_by_location_rules($filter);
        $filtered = array();
        $sorted = array();

        if ( ! empty( $group ) ) {
            foreach ($fields as $field ) {
                if(!isset($field['group'])){
                    continue;
                }
                if ( $group == $field['group'] ) {
                    $filtered[] = $field;
                }
            }
        } else {
            $filtered = $fields;
        }

        if(!empty($sorted) && count($filtered)) {
            foreach ($filtered as $key => $value) {
                if(!isset($value['sort'])){
                    continue;
                }
                $sorted[$key] = $value['sort'];
            }

            if(!empty($sorted) && count($sorted)) {
                array_multisort($sorted, SORT_ASC, SORT_NUMERIC, $filtered);
            }
        }

        return apply_filters( 'advanced-product/fields', $filtered );
    }

    public static function get_core_fields(){
        $store_id   = static::_get_store_id(__METHOD__);

        if(isset(static::$cache[$store_id])){
            return static::$cache[$store_id];
        }

        $core_fields = array(
            'ap_branch' => array (
                'label' => 'Branch',
                'name' => 'ap_branch',
                'type' => 'taxonomy',
                'taxonomy' => 'ap_branch',
                'sort' => 0,
                'group' => 'overview',
                'allow_null' => 0
            ),

        );

        $core_fields    = apply_filters('advanced-product/fields/register_core_field', $core_fields);

        return static::$cache[$store_id]    = $core_fields;
    }

    /**
     * Returns all values of given meta key
     * @param  string $key    [description]
     * @param  string $type   [description]
     * @param  string $status [description]
     * @return [type]         [description]
     */
    public static function get_meta_values( $key = '', $type = 'post', $status = 'publish' ) {

        $store_id   = static::_get_store_id(__METHOD__);

        if(isset(static::$cache[$store_id])){
            return static::$cache[$store_id];
        }

        global $wpdb;

        if( empty( $key ) )
            return;

        $r = $wpdb->get_col( $wpdb->prepare( "
	        SELECT pm.meta_value FROM {$wpdb->postmeta} pm
	        LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
	        WHERE pm.meta_key = '%s'
	        AND p.post_status = '%s'
	        AND p.post_type = '%s'
	    ", $key, $status, $type ) );

        if($r && count($r)){
            static::$cache[$store_id]   = $r;
            return $r;
        }

        return array();
    }
    public static function get_price_symbol_for_position( $position = 'prepend' ) {

        $symbol 		= get_option( 'options_ap_currency_symbol', '$' );
        $option 		= get_option( 'options_ap_symbol_placement', 'prepend' );

        if ( ('prepend' == $option && 'prepend' == $position) ||
            ( 'append' == $option && 'append' == $position ) ) {
            return $symbol;
        }
        else {
            return '';
        }
    }

    /**
     * Get acf fields by custom field
     * @param int $post_id Id of custom field.
     * @return array acf field
     * */
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

    /**
     * Get all fields without group field terms
     * @param  array $options An options of get field query.
     * @return  array|bool fields of ap_custom_field post type
     * */
    public static function get_fields_without_group_field($options = array()){
        $post_type  = 'ap_custom_field';
        $taxonomy   = 'ap_group_field';
        $term_args  = array(
            'taxonomy'      => $taxonomy,
            'fields'        => 'ids',
            'hide_empty'    => false,
        );

        if(isset($options['taxonomy_options']) && !empty($options['taxonomy_options'])) {
            $term_args = array_merge($term_args, $options['taxonomy_options']);
            unset($options['taxonomy_options']);
        }

        $terms      =  \get_terms( $term_args );

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

        if(empty($query) || \is_wp_error($query)){
            return false;
        }

        $fields = $query -> get_posts();
        wp_reset_query();

        return static::$cache[$store_id] = $fields;

    }

    /**
     * Get acf fields with empty group field taxonomy
     * @return array|bool
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
     * Get group fields by branch slug
     * */
    public static function get_group_fields_by_branch_slug($branch_slug){

        $store_id   = static::_get_store_id(__METHOD__, $branch_slug);

        if(isset(static::$cache[$store_id])){
            return static::$cache[$store_id];
        }

        // Get branch by branch_slug
        $branches = get_terms ([
            'slug'     => $branch_slug,
            'taxonomy' => 'ap_branch',
            'hide_empty' => false,
        ] );

        if(!$branches || is_wp_error($branches)){
            return false;
        }

        $data   = array();
        // Get all group fields assigned to branch
        foreach ($branches as $branch) {
            $gfields_assigned = \get_field('group_field_assigned', 'term_' . $branch->term_id);

            if(!empty($gfields_assigned)) {
                foreach ($gfields_assigned as $i => $group_slug){
                    // Get group field info
                    $group  = get_term_by('slug', $group_slug, 'ap_group_field');

                    if(!empty($group) && !is_wp_error($group)){
                        $data[] = $group;
                    }
                }
            }
        }

        if(!empty($data)){
            return static::$cache[$store_id]    = $data;
        }

        return false;
    }

    /**
     * Get fields by branch slug
     * */
    public static function get_fields_by_branch_slug($branch_slug){

        $store_id   = static::_get_store_id(__METHOD__, $branch_slug);

        if(isset(static::$cache[$store_id])){
            return static::$cache[$store_id];
        }

        // Get branch by branch_slug
        $branches = get_terms ([
            'slug'     => $branch_slug,
            'taxonomy' => 'ap_branch',
            'hide_empty' => false,
        ] );

        if(!$branches || is_wp_error($branches)){
            return false;
        }

        $data   = array();

        // Get all group fields assigned to branch
        foreach ($branches as $branch) {
//                $gfields_assigned = \get_field('group_field_assigned', 'ap_branch_' . $branch->term_id);
            $gfields_assigned = \get_field('group_field_assigned', 'term_' . $branch->term_id);

            if(!empty($gfields_assigned)) {

                $gid = md5('property');
                $goptions = array(
//                    'id' => 'acf_' . md5('product_property'),
//                    'title' => __('Properties', 'advanced-product'),
                    'fields' => array(),
                    'location' => array(
                        array(
                            array(
                                'param' => 'post_type',
                                'operator' => '==',
                                'value' => 'ap_product',
                                'order_no' => 0,
                                'group_no' => 0,
                            ),
                        ),
                    ),
                    'options' => array(
                        'position' => 'normal',
                        'style' => 'default',
                        'layout' => 'default',
                        //                        'hide_on_screen' => array (
                        //                            /*'the_content',*/ 'custom_fields'
                        //                        ),
                        'hide_on_screen' => array(),
                    ),
                    'menu_order' => 0,
                );

                foreach ($gfields_assigned as $i => $group_slug){
                    // Get group field info
                    $group  = get_term_by('slug', $group_slug, 'ap_group_field');

                    if(!empty($group) && !is_wp_error($group)){
                        $cfields = AP_Custom_Field_Helper::get_fields_by_group_field_slug($group_slug);

                        $fields = array();
                        if($cfields){
                            foreach($cfields as $cfield){
                                $fields[]   = FieldHelper::get_custom_field_option_by_id($cfield->ID);
                            }
                        }

                        if(!empty($fields)){
                            // Register fields for acf
                            $goptions['id'] = (!empty($group->slug) ? $group->slug : $gid);
                            $goptions['title'] = (!empty($group->name)) ? $group->name : '';
                            $goptions['menu_order'] = $i;
                            $goptions['fields'] = $fields;

                            $data[] = $goptions;
                        }
                    }
                }
            }
        }

        if(!empty($data)){
            return static::$cache[$store_id]    = $data;
        }

        return false;
    }

    /**
     * Add term_order field to table wp_terms
     * */
    public static function add_term_order_field()
    {
        global $wpdb;

        if (!$result = static::term_order_exists())
        {
            $query = "ALTER TABLE $wpdb->terms ADD `term_order` INT( 4 ) NULL DEFAULT '0'";
            $result = $wpdb->query($query);
        }
    }

    /**
     * Check term order field exists in table wp_terms
     * It added by this plugin
     * */
    public static function term_order_exists(){
        global $wpdb;

        $store_id   = md5(__METHOD__);

        if(isset(static::$cache[$store_id])){
            return static::$cache[$store_id];
        }

        $query = "SHOW COLUMNS FROM $wpdb->terms 
                        LIKE 'term_order'";
        $result = $wpdb->query($query);

        if($result){
            static::$cache[$store_id]   = $result;
            return $result;
        }

        return false;
    }
}