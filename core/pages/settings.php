<?php

namespace Advanced_Product\Page;

use Advanced_Product\Base;
use Advanced_Product\Helper\FieldHelper;

defined('ADVANCED_PRODUCT') or exit();

class Settings extends Base {

    protected $build_in;

    public function __construct($core = null, $post_type = null)
    {
        parent::__construct($core, $post_type);

//        register_activation_hook( ADVANCED_PRODUCT . '/' . ADVANCED_PRODUCT, array( $this, 'register_admin_fields' ) );

        $this -> build_in   = FieldHelper::get_core_fields();

    }

    public function hooks()
    {
        parent::hooks();

//        add_action( 'plugins_loaded', array( $this, 'register_admin_fields' ) );
//        add_action( 'advanced-product/after_init', array( $this, 'register_admin_fields' ) );
        add_action( 'plugins_loaded', array( $this, 'register_admin_fields' ) );
//        add_action( 'init', array( $this, 'register_field_groups' ) );
    }

    public function register_admin_fields()
    {
        if (function_exists('\acf_add_options_sub_page') && (function_exists('register_field_group') || function_exists('acf_add_local_field_group'))) {

            \acf_add_options_sub_page(array(
                'title' => __('Settings', 'advanced-product'),
                'parent' => 'edit.php?post_type=ap_product',
                'capability' => 'manage_options'
            ));

            $this -> register_field_groups();
        }
    }

    public function register_field_groups()
    {
        if (function_exists('\acf_add_options_sub_page') && (function_exists('register_field_group')
                || function_exists('acf_add_local_field_group'))) {

            $built_in_fields = $this -> build_in;


            $choices = array();
            if (!empty($built_in_fields)) {
                foreach ($built_in_fields as $field) {
                    $choices[$field['name']] = $field['label'];
                }
            }

            $register_field_group   = 'register_field_group';
            if(function_exists('acf_add_local_field_group')){
                $register_field_group   = 'acf_add_local_field_group';
            }

            $fields = array (
                array (
                    'key' => 'field_52910fcad4ef944',
                    'label' => __( 'General', 'advanced-product' ),
                    'name' => '',
                    'type' => 'tab',
                ),
                array (
                    'key' => 'field_5281609138d88',
                    'label' => __( 'Mileage unit', 'advanced-product' ),
                    'name' => 'ap_milage_unit',
                    'type' => 'radio',
                    'choices' => array (
                        'mi' => __( 'Miles (mi)', 'advanced-product' ),
                        'km' => __( 'Kilometer (km)', 'advanced-product' ),
                    ),
                    'other_choice' => 0,
                    'save_other_choice' => 0,
                    'default_value' => 'mi',
                    'layout' => 'horizontal',
                ),
                array (
                    'key' => 'field_5281610b906e3',
                    'label' => __( 'Currency symbol', 'advanced-product' ),
                    'name' => 'ap_currency_symbol',
                    'type' => 'radio',
                    'choices' => array (
                        '$' => '$',
                        '€' => '€',
                        '£' => '£',
                        '¥' => '¥',
                    ),
                    'other_choice' => 1,
                    'save_other_choice' => 1,
                    'default_value' => '$',
                    'layout' => 'vertical',
                ),
                array (
                    'key' => 'field_5281618b906e4',
                    'label' => __( 'Symbol placement', 'advanced-product' ),
                    'name' => 'ap_symbol_placement',
                    'type' => 'radio',
                    'choices' => array (
                        'prepend' => __( 'Before numbers', 'advanced-product' ),
                        'append' => __( 'After numbers', 'advanced-product' ),
                    ),
                    'other_choice' => 0,
                    'save_other_choice' => 0,
                    'default_value' => 'prepend',
                    'layout' => 'vertical',
                ),
                array (
                    'key'           => 'field_52816229906e5',
                    'label'         => __( 'Thousands separator', 'advanced-product' ),
                    'name'          => 'ap_price_thousands_sep',
                    'type'          => 'text',
                    'default_value' => '.',
                ),
                array (
                    'key' => 'field_618e30cd79924',
                    'label' => __( 'Decimal separator', 'advanced-product' ),
                    'name' => 'ap_price_decimal_sep',
                    'type' => 'text',
                    'default_value' => ',',
                ),
                array (
                    'key' => 'field_618e37d0481ac',
                    'label' => __( 'Number of decimals', 'advanced-product' ),
                    'name' => 'ap_price_num_decimals',
                    'type' => 'number',
                    'default_value' => '0',
                ),
                array (
                    'key' => 'field_6306f42b88ed8',
                    'label' => __( 'Archive Product', 'advanced-product' ),
                    'name' => '',
                    'type' => 'tab',
                ),
                array (
                    'key'           => 'field_6306f48249024',
                    'type'          => 'radio',
                    'layout'        => 'horizontal',
                    'name'          => 'ap_show_archive_compare_button',
                    'label'         => __( 'Show Compare Button', 'advanced-product' ),
                    'default_value' => 1,
                    'choices'       => array(
                        1   => __('Yes', 'advanced-product'),
                        0   => __('No', 'advanced-product'),
                    ),
                ),
                array (
                    'key'           => 'field_630ccbd5a0a19',
                    'type'          => 'radio',
                    'layout'        => 'horizontal',
                    'name'          => 'ap_show_archive_quickview_button',
                    'label'         => __( 'Show Quick View Button', 'advanced-product' ),
                    'default_value' => 1,
                    'choices'       => array(
                        1   => __('Yes', 'advanced-product'),
                        0   => __('No', 'advanced-product'),
                    ),
                ),
                array (
                    'key'           => 'field_634d1f9ef0431',
                    'type'          => 'radio',
                    'layout'        => 'horizontal',
                    'name'          => 'ap_show_archive_custom_field_icon',
                    'label'         => __( 'Show Custom Field Icon', 'advanced-product' ),
                    'default_value' => 0,
                    'choices'       => array(
                        1   => __('Yes', 'advanced-product'),
                        0   => __('No', 'advanced-product'),
                    ),
                ),
                array (
                    'key'           => 'field_631aac11e8f20',
                    'type'          => 'select',
                    'layout'        => 'horizontal',
                    'name'          => 'ap_archive_product_order_by',
                    'label'         => __( 'Product Order', 'advanced-product' ),
                    'default_value' => 'rdate',
                    'choices'       => array(
                        'rdate'   => __('Most recent first', 'advanced-product'),
                        'date'   => __('Oldest First', 'advanced-product'),
                        'alpha'   => __('Title Alphabetical', 'advanced-product'),
                        'ralpha'   => __('Title Reverse Alphabetical', 'advanced-product'),
                        'author'   => __('Author Alphabetical', 'advanced-product'),
                        'rauthor'   => __('Author Reverse Alphabetical', 'advanced-product'),
                        'hits'   => __('Most Hits', 'advanced-product'),
                        'rhits'   => __('Least Hits', 'advanced-product'),
                        'price'   => __('Minimum Price First', 'advanced-product'),
                        'rprice'   => __('Maximum Price First', 'advanced-product'),
                    ),
                ),
                array (
                    'key'           => 'field_631aac11e8f22',
                    'type'          => 'select',
                    'layout'        => 'horizontal',
                    'name'          => 'ap_archive_sold_product_order_by',
                    'label'         => __( 'Sold Product Order', 'advanced-product' ),
                    'default_value' => '',
                    'choices'       => array(
                        ''          => __('- Select Sold Product Order -', 'advanced-product'),
                        'top'       => __('Top of the list', 'advanced-product'),
                        'bottom'    => __('Bottom of the list', 'advanced-product'),
                    ),
                ),
                array (
                    'key'           => 'field_633be5d141b34',
                    'type'          => 'select',
                    'layout'        => 'horizontal',
                    'name'          => 'ap_archive_product_order_by_custom_field',
                    'label'         => __( 'Custom Field Order', 'advanced-product' ),
                    'default_value' => 'order',
                    'choices'       => array(
                        'rdate'     => __('Most recent first', 'advanced-product'),
                        'date'      => __('Oldest First', 'advanced-product'),
                        'alpha'     => __('Title Alphabetical', 'advanced-product'),
                        'ralpha'    => __('Title Reverse Alphabetical', 'advanced-product'),
                        'order'     => __('Custom Field Order', 'advanced-product'),
                        'rorder'    => __('Custom Field Reverse Order', 'advanced-product'),
                    ),
                ),
                array (
//                        'key' => 'field_'.uniqid(),
                    'key' => 'field_61b705ba820c5',
                    'label' => __( 'Single Product', 'advanced-product' ),
                    'name' => '',
                    'type' => 'tab',
                ),
                array(
                    'key'       => 'field_61b70531f3f97',
                    'label'     => __('Show Date', 'advanced-product'),
                    'name'      => 'ap_show_date',
                    'type'      => 'radio',
                    'choices'   => array(
                        1   => __('Yes', 'advanced-product'),
                        0   => __('No', 'advanced-product'),
                    ),
                    'default_value' => 0,
                    'layout'    => 'horizontal',
                ),
                array(
                    'key'       => 'field_61b7054b01a44',
                    'label'     => __('Show Author', 'advanced-product'),
                    'name'      => 'ap_show_author',
                    'type'      => 'radio',
                    'choices'   => array(
                        1   => __('Yes', 'advanced-product'),
                        0   => __('No', 'advanced-product'),
                    ),
                    'default_value' => 0,
                    'layout'    => 'horizontal',
                ),
                array(
                    'key'       => 'field_61b7055a3c27c',
                    'label'     => __('Show Post View', 'advanced-product'),
                    'name'      => 'ap_show_post_view',
                    'type'      => 'radio',
                    'choices'   => array(
                        1   => __('Yes', 'advanced-product'),
                        0   => __('No', 'advanced-product'),
                    ),
                    'default_value' => 0,
                    'layout'    => 'horizontal',
                ),
                array(
                    'key'       => 'field_61b705670c3ab',
                    'label'     => __('Show Comment Count', 'advanced-product'),
                    'name'      => 'ap_show_comment_count',
                    'type'      => 'radio',
                    'choices'   => array(
                        1   => __('Yes', 'advanced-product'),
                        0   => __('No', 'advanced-product'),
                    ),
                    'default_value' => 0,
                    'layout'    => 'horizontal',
                ),
                array (
                    'key'           => 'field_529db63c010e5',
                    'type'          => 'radio',
                    'layout'        => 'horizontal',
                    'name'          => 'ap_show_compare_button',
                    'label'         => __( 'Show Compare Button', 'advanced-product' ),
                    'default_value' => 1,
                    'choices'       => array(
                        1   => __('Yes', 'advanced-product'),
                        0   => __('No', 'advanced-product'),
                    ),
                ),
                array (
                    'key'           => 'field_634e60321041b',
                    'type'          => 'radio',
                    'layout'        => 'horizontal',
                    'name'          => 'ap_show_custom_field_icon',
                    'label'         => __( 'Show Custom Field Icon', 'advanced-product' ),
                    'default_value' => 0,
                    'choices'       => array(
                        1   => __('Yes', 'advanced-product'),
                        0   => __('No', 'advanced-product'),
                    ),
                ),
                array (
                    'key'           => 'field_633bfbb3cd73a',
                    'type'          => 'select',
                    'layout'        => 'horizontal',
                    'name'          => 'ap_order_by_custom_field',
                    'label'         => __( 'Custom Field Order', 'advanced-product' ),
                    'default_value' => 'order',
                    'choices'       => array(
                        'rdate'     => __('Most recent first', 'advanced-product'),
                        'date'      => __('Oldest First', 'advanced-product'),
                        'alpha'     => __('Title Alphabetical', 'advanced-product'),
                        'ralpha'    => __('Title Reverse Alphabetical', 'advanced-product'),
                        'order'     => __('Custom Field Order', 'advanced-product'),
                        'rorder'    => __('Custom Field Reverse Order', 'advanced-product'),
                    ),
                ),
            );

            $fields = \apply_filters('advanced-product/settings/fields', $fields);

            call_user_func($register_field_group, array (
                'id' => 'advanced_product_settings_page',
                'title' => __( 'Settings', 'advanced-product' ),
                'fields' => $fields,

                'location' => array (
                    array (
                        array (
                            'param' => 'options_page',
                            'operator' => '==',
                            'value' => 'acf-options-settings',
                            'order_no' => 0,
                            'group_no' => 0,
                        ),
                    ),
                ),

                'options' => array (
                    'position' => 'normal',
                    'layout' => 'no_box',
                    'hide_on_screen' => array (
                    ),
                ),
                'menu_order' => 0,
            ));

        }
    }

}