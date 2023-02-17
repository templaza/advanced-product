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

        $text_domain    = AP_Functions::get_my_text_domain();
        $color_choices = array_unique( array_merge( array (
            __( 'Silver', $text_domain ),
            __( 'Black', $text_domain ),
            __( 'White', $text_domain ),
            __( 'Red', $text_domain ),
            __( 'Blue', $text_domain ),
            __( 'Brown/Beige', $text_domain ),
            __( 'Yellow', $text_domain ),
            __( 'Green', $text_domain ),
        ), static::get_meta_values( 'color', 'ap_product' ) ));
        $color_choices = array_combine( $color_choices, $color_choices);

        $int_color_choices = array_unique( array_merge( array (
            'black' => __( 'Black', $text_domain ),
            'white' => __( 'White', $text_domain ),
            'brown' => __( 'Brown (Leather)', $text_domain )
        ), static::get_meta_values( 'interior', 'ap_product' ) ));
        $int_color_choices = array_combine( $int_color_choices, $int_color_choices);

        $core_fields = array(
            'ap_branch' => array (
                'label' => __( 'Branch', $text_domain ),
                'name' => 'ap_branch',
                'type' => 'taxonomy',
                'taxonomy' => 'ap_branch',
                'sort' => 0,
                'group' => 'overview',
                'allow_null' => 0
            ),
            'ap_category' => array (
                'label' => __( 'Category', $text_domain ),
                'name' => 'ap_category',
                'instructions' => __( 'If you do not see Make or can not choose it please edit and save again in make manager', $text_domain ),
                'type' => 'taxonomy',
                'taxonomy' => 'ap_category',
                'sort' => 1,
                'group' => 'overview',
                'field_type' => 'select',
                'allow_null' => 0
            ),
            'pricetext' => array (
                'label' => __( 'Text Price', $text_domain ),
                'name' => 'pricetext',
                'type' => 'text',
                'instructions' => __( 'Contact get price', $text_domain ),
                'default_value' => '',
                'placeholder' => __( 'Price Contact', $text_domain ),
                'sort' => 18,
                'group'=>'pricing',
            ),
            'pricelink' => array (
                'label' => __( 'Url Price', $text_domain ),
                'name' => 'pricelink',
                'type' => 'text',
                'instructions' => __( 'URL get price', $text_domain ),
                'default_value' => '',
                'placeholder' => __( 'Url Price', $text_domain ),
                'sort' => 19,
                'group'=>'pricing',
            ),
            'ap_product_status' => array(
//                'key'   => 'field_6192374baaf91',
                'label' => __( 'Product sale/rent', $text_domain ),
                'name' => 'ap_product_status',
                'instructions' => '',
                'type' => 'radio',
                'choices' => array(
                    'sale' => __( 'For Sale', $text_domain ),
                    'rent' => __( 'For Rent', $text_domain ),
                ),
                'default_value' => 'sale',
                'other_choice' => 0,
                'sort' => 9,
                'group' => 'pricing',
            ),
            'price' => array(
                'label' => __( 'Price', $text_domain ),
                'name' => 'price',
                'instructions' => __( "The price that the customer will have to pay.", $text_domain ),
                'type' => 'number',
                'default_value' => '',
                'placeholder' => '',
                'prepend' => static::get_price_symbol_for_position('prepend'),
                'append' => static::get_price_symbol_for_position('append'),
                'min' => 0,
                'max' => '8000000000',
                'step' => '',
                'group' => 'pricing',
                'sort' => 15,
            ),
            'msrp' => array(
                'label' => __( 'MSRP', $text_domain ),
                'name' => 'msrp',
                'instructions' => __( "Use integers to set the listing price.", $text_domain ),
                'type' => 'number',
                'default_value' => '',
                'placeholder' => '',
                'prepend' => static::get_price_symbol_for_position('prepend'),
                'append' => static::get_price_symbol_for_position('append'),
                'min' => 0,
                'max' => '8000000000',
                'step' => '',
                'group' => 'pricing',
                'sort' => 10,
            ),
            'pricerental' => array(
                'label' => __( 'Price Rental', $text_domain ),
                'name' => 'pricerental',
                'instructions' => __( "Prices for rent a day or a week", $text_domain ),
                'type' => 'number',
                'default_value' => '',
                'placeholder' => '',
                'prepend' => static::get_price_symbol_for_position('prepend'),
                'append' => static::get_price_symbol_for_position('append'),
                'min' => 0,
                'max' => '8000000000',
                'step' => '',
                'group' => 'pricing',
                'sort' => 16,
//                'conditional_logic' => array(
//                    array(
//                        array (
////                            'field' => 'ap_product_status',
//                            'field' => 'field_6192374baaf91',
//                            'operator' => '==',
//                            'value' => 'rent',
//                        ),
//                    ),
//                ),
            ),
            'time_rental' => array (
                'label' => __( 'Time unit for Rent', $text_domain ),
                'name' => 'time_rental',
                'type' => 'text',
                'default_value' => 'day',
                'placeholder' => __( 'day', $text_domain ),
                'group' => 'pricing',
                'sort' => 17,
            ),
            'registration' => array (
                'label' => __( 'Registration date', $text_domain ),
                'name' => 'registration',
                'type' => 'number',
                'instructions' => __( 'The year of first registration', $text_domain ),
                'placeholder' => __( 'e.g. 2009', $text_domain ),
                'min' => 1950,
                'max' => date( 'Y' ) + 1,
                'default_value' => date( 'Y' ),
                'sort' => 15,
            ),
            'milage' => array(
                'label' => __( 'Mileage', $text_domain ),
                'name' => 'milage',
                'type' => 'number',
                'instructions' => __( 'The number of miles travelled or covered', $text_domain ),
                'default_value' => '',
                'placeholder' => __( 'e.g. 70000', $text_domain ),
                'prepend' => '',
                'append' => get_option( 'options_ap_milage_unit', 'mi' ),
                'sort' => 20
            ),
            'condition' => array(
                'label' => __( 'Condition', $text_domain ),
                'name' => 'condition',
                'instructions' => '',
                'type' => 'radio',
                'choices' => array(
                    'new' => __( 'New', $text_domain ),
                    'used' => __( 'Used', $text_domain ),
                    'preowned' => __( 'Certified Pre-Owned', $text_domain )
                ),
                'default_value' => 'new',
                'other_choice' => 0,
                'sort' => 30
            ),
            'color' => array(
                'label' => __( 'Exterior Color', $text_domain ),
                'name' => 'color',
                'type' => 'radio',
                'choices' => $color_choices,
                'other_choice' => 1,
                'save_other_choice' => 1,
                'default_value' => 'silver',
                'layout' => 'vertical',
                'sort' => 40,
            ),
            'interior' => array(
                'label' => __( 'Interior Color', $text_domain ),
                'name' => 'interior',
                'type' => 'radio',
                'choices' => $int_color_choices,
                'other_choice' => 1,
                'save_other_choice' => 1,
                'default_value' => 'black',
                'layout' => 'vertical',
                'sort' => 50,
            ),
            'transmission' => array(
                'label' => __( 'Transmission', $text_domain ),
                'name' => 'transmission',
                'type' => 'radio',
                'choices' => array (
                    'auto' => __( 'Automatic', $text_domain ),
                    'manual' => __( 'Manual', $text_domain ),
                ),
                'default_value' => '',
                'layout' => 'horizontal',
                'sort' => 60,
            ),
            'engine' => array (
                'label' => __( 'Engine', $text_domain ),
                'name' => 'engine',
                'instructions' => __( 'The displacement the engine gives in Litres', $text_domain ),
                'append' => 'L',
                'placeholder' => '4,1',
                'sort' => 70,

                'min' => 0,
                'max' => 10
            ),
            'drivetrain' => array(
                'label' => __( 'Drivetrain', $text_domain ),
                'name' => 'drivetrain',
                'type' => 'radio',
                'choices' => array (
                    'fwd' => __( 'FWD', $text_domain ),
                    'rwd' => __( 'RWD', $text_domain ),
                    '4wd' => __( '4WD', $text_domain ),
                ),
                'default_value' => '',
                'layout' => 'horizontal',
                'sort' => 90,
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