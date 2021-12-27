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
        add_action( 'advanced-product/after_init', array( $this, 'register_admin_fields' ) );
//        add_action( 'init', array( $this, 'register_field_groups' ) );
    }

    public function register_admin_fields()
    {
        if (function_exists('\acf_add_options_sub_page') && (function_exists('register_field_group') || function_exists('acf_add_local_field_group'))) {

            \acf_add_options_sub_page(array(
                'title' => __('Settings', $this -> text_domain),
                'parent' => 'edit.php?post_type=ap_product',
                'capability' => 'manage_options'
            ));

//            if(!function_exists('acf_add_local_field_group')){
                $this -> register_field_groups();
//            }
        }
    }

    public function register_field_groups()
    {
        if (function_exists('\acf_add_options_sub_page') && (function_exists('register_field_group')
                || function_exists('acf_add_local_field_group'))) {

//            $built_in_fields = FieldHelper::get_core_fields();
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

            call_user_func($register_field_group, array (
                'id' => 'advanced_product_settings_page',
                'title' => __( 'Settings', $this -> text_domain ),
                'fields' => array (
                    array (
                        'key' => 'field_52910fcad4ef944',
                        'label' => __( 'General', $this -> text_domain ),
                        'name' => '',
                        'type' => 'tab',
                    ),
                    array (
                        'key' => 'field_5281609138d88',
                        'label' => __( 'Mileage unit', $this -> text_domain ),
                        'name' => 'ap_milage_unit',
                        'type' => 'radio',
                        'choices' => array (
                            'mi' => __( 'Miles (mi)', $this -> text_domain ),
                            'km' => __( 'Kilometer (km)', $this -> text_domain ),
                        ),
                        'other_choice' => 0,
                        'save_other_choice' => 0,
                        'default_value' => 'mi',
                        'layout' => 'horizontal',
                    ),
                    array (
                        'key' => 'field_5281610b906e3',
                        'label' => __( 'Currency symbol', $this -> text_domain ),
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
                        'label' => __( 'Symbol placement', $this -> text_domain ),
                        'name' => 'ap_symbol_placement',
                        'type' => 'radio',
                        'choices' => array (
                            'prepend' => __( 'Before numbers', $this -> text_domain ),
                            'append' => __( 'After numbers', $this -> text_domain ),
                        ),
                        'other_choice' => 0,
                        'save_other_choice' => 0,
                        'default_value' => 'prepend',
                        'layout' => 'vertical',
                    ),
                    array (
                        'key'           => 'field_52816229906e5',
                        'label'         => __( 'Thousands separator', $this -> text_domain ),
                        'name'          => 'ap_price_thousands_sep',
                        'type'          => 'text',
                        'default_value' => '.',
                    ),
                    array (
                        'key' => 'field_618e30cd79924',
                        'label' => __( 'Decimal separator', $this -> text_domain ),
                        'name' => 'ap_price_decimal_sep',
                        'type' => 'text',
                        'default_value' => ',',
                    ),
                    array (
                        'key' => 'field_618e37d0481ac',
                        'label' => __( 'Number of decimals', $this -> text_domain ),
                        'name' => 'ap_price_num_decimals',
                        'type' => 'number',
                        'default_value' => '0',
                    ),
                    array (
//                        'key' => 'field_'.uniqid(),
                        'key' => 'field_61b705ba820c5',
                        'label' => __( 'Single Product', $this -> text_domain ),
                        'name' => '',
                        'type' => 'tab',
                    ),
                    array(
                        'key'       => 'field_61b70531f3f97',
                        'label'     => __('Show Date', $this -> text_domain),
                        'name'      => 'ap_show_date',
                        'type'      => 'radio',
                        'choices'   => array(
                            1   => __('Yes', $this -> text_domain),
                            0   => __('No', $this -> text_domain),
                        ),
                        'default_value' => 0,
                        'layout'    => 'horizontal',
                    ),
                    array(
                        'key'       => 'field_61b7054b01a44',
                        'label'     => __('Show Author', $this -> text_domain),
                        'name'      => 'ap_show_author',
                        'type'      => 'radio',
                        'choices'   => array(
                            1   => __('Yes', $this -> text_domain),
                            0   => __('No', $this -> text_domain),
                        ),
                        'default_value' => 0,
                        'layout'    => 'horizontal',
                    ),
                    array(
                        'key'       => 'field_61b7055a3c27c',
                        'label'     => __('Show Post View', $this -> text_domain),
                        'name'      => 'ap_show_post_view',
                        'type'      => 'radio',
                        'choices'   => array(
                            1   => __('Yes', $this -> text_domain),
                            0   => __('No', $this -> text_domain),
                        ),
                        'default_value' => 0,
                        'layout'    => 'horizontal',
                    ),
                    array(
                        'key'       => 'field_61b705670c3ab',
                        'label'     => __('Show Comment Count', $this -> text_domain),
                        'name'      => 'ap_show_comment_count',
                        'type'      => 'radio',
                        'choices'   => array(
                            1   => __('Yes', $this -> text_domain),
                            0   => __('No', $this -> text_domain),
                        ),
                        'default_value' => 0,
                        'layout'    => 'horizontal',
                    ),
//                    array (
//                        'key' => 'field_529db63c010e5',
//                        'label' => __( 'Fields', $this -> text_domain ),
//                        'name' => '',
//                        'type' => 'tab',
//                    ),
//                    array (
//                        'key' => 'field_5288dd811ed6e',
//                        'label' => __( 'Fields to exclude', $this -> text_domain ),
//                        'name' => 'ap_excluded_fields',
//                        'type' => 'checkbox',
//                        'instructions' => __( 'Selected fields will not be used', $this -> text_domain ),
//                        'choices' => $choices,
//                        'default_value' => '',
//                        'layout' => 'horizontal',
//                    ),
//                    array (
//                        'key' => 'field_52816a1633584',
//                        'label' => __( 'Add custom fields', $this -> text_domain ),
//                        'name' => 'ap_custom_fields',
//                        'type' => 'flexible_content',
//                        'layouts' => array (
//                            array (
//                                'label' => __( 'Number field', $this -> text_domain ),
//                                'name' => 'ap_number_field',
//                                'display' => 'table',
//                                'min' => '',
//                                'max' => '',
//                                'sub_fields' => array (
//                                    array (
//                                        'key' => 'field_52816abf33585',
//                                        'label' => __( 'Name', $this -> text_domain ),
//                                        'name' => 'ap_name',
//                                        'type' => 'text',
//                                        'column_width' => 35,
//                                        'default_value' => '',
//                                        'placeholder' => __( 'E.g. "Horsepower"', $this -> text_domain ),
//                                        'prepend' => '',
//                                        'append' => '',
//                                        'formatting' => 'none',
//                                        'maxlength' => '',
//                                    ),
//                                    array (
//                                        'key' => 'field_52816af633586',
//                                        'label' => __( 'Minimum value', $this -> text_domain ),
//                                        'name' => 'ap_min',
//                                        'type' => 'number',
//                                        'column_width' => '',
//                                        'default_value' => 0,
//                                        'placeholder' => '',
//                                        'prepend' => '',
//                                        'append' => '',
//                                        'min' => '',
//                                        'max' => '',
//                                        'step' => '',
//                                    ),
//                                    array (
//                                        'key' => 'field_52816b8d33588',
//                                        'label' => __( 'Maximum value', $this -> text_domain ),
//                                        'name' => 'ap_max',
//                                        'type' => 'number',
//                                        'column_width' => '',
//                                        'default_value' => 1000,
//                                        'placeholder' => '',
//                                        'prepend' => '',
//                                        'append' => '',
//                                        'min' => '',
//                                        'max' => '',
//                                        'step' => '',
//                                    ),
//                                    array (
//                                        'key' => 'field_52816ba133589',
//                                        'label' => __( 'Append', $this -> text_domain ),
//                                        'name' => 'ap_append',
//                                        'type' => 'text',
//                                        'column_width' => '',
//                                        'default_value' => '',
//                                        'placeholder' => __( 'e.g. "PS"', $this -> text_domain),
//                                        'prepend' => '',
//                                        'append' => '',
//                                        'formatting' => 'none',
//                                        'maxlength' => 10,
//                                    ),
//                                ),
//                            ),
//                            array (
//                                'label' => __( 'Options field', $this -> text_domain ),
//                                'name' => 'ap_option',
//                                'display' => 'table',
//                                'min' => '',
//                                'max' => '',
//                                'sub_fields' => array (
//                                    array (
//                                        'key' => 'field_52816dc9ffee1',
//                                        'label' => __( 'Name', $this -> text_domain ),
//                                        'name' => 'ap_name',
//                                        'type' => 'text',
//                                        'column_width' => 35,
//                                        'default_value' => '',
//                                        'placeholder' => __( 'E.g. "Fuel Type"', $this -> text_domain ),
//                                        'prepend' => '',
//                                        'append' => '',
//                                        'formatting' => 'html',
//                                        'maxlength' => '',
//                                    ),
//                                    array (
//                                        'key' => 'field_52816e92ffee6',
//                                        'label' => __( 'Choices', $this -> text_domain ),
//                                        'name' => 'ap_choices',
//                                        'type' => 'text',
//                                        'instructions' => __( 'Options separated by comma e.g. Petrol, Diesel, Gas, Hybrid', $this -> text_domain ),
//                                        'column_width' => '',
//                                        'default_value' => '',
//                                        'placeholder' => '',
//                                        'prepend' => '',
//                                        'append' => '',
//                                        'formatting' => 'html',
//                                        'maxlength' => '',
//                                    )
//                                ),
//                            ),
//                            array (
//                                'label' => __( 'Text field', $this -> text_domain ),
//                                'name' => 'ap_text',
//                                'display' => 'table',
//                                'sub_fields' => array (
//                                    array (
////                                        'key' => 'field_'.uniqid(),
//                                        'key' => 'field_52816dc9ffee9',
//                                        'label' => __( 'Name', $this -> text_domain ),
//                                        'name' => 'ap_name',
//                                        'type' => 'text',
//                                        'column_width' => 35,
//                                        'default_value' => '',
//                                        'placeholder' => __( 'Field name', $this -> text_domain ),
//                                        'prepend' => '',
//                                        'append' => '',
//                                        'formatting' => 'html',
//                                        'maxlength' => '',
//                                    ),
//                                    array (
//                                        'key' => 'field_6188dc70e0f0b',
//                                        'label' => __( 'Value', $this -> text_domain ),
//                                        'name' => 'ap_textvalue',
//                                        'type' => 'text',
//                                        'column_width' => '',
//                                        'default_value' => '',
//                                        'placeholder' => '',
//                                        'prepend' => '',
//                                        'append' => '',
//                                        'formatting' => 'html',
//                                        'maxlength' => '',
//                                    )
//                                ),
//                            ),
//                            array (
//                                'label' => __( 'Image Field', $this -> text_domain ),
//                                'name' => 'ap_image',
//                                'display' => 'table',
//                                'sub_fields' => array (
//                                    array (
//                                        'key' => 'field_6188dc70e0dff',
//                                        'label' => __( 'Name', $this -> text_domain ),
//                                        'name' => 'ap_name',
//                                        'type' => 'text',
////                                        'column_width' => 35,
//                                        'default_value' => '',
//                                        'placeholder' => __( 'Field name', $this -> text_domain ),
//                                        'prepend' => '',
//                                        'append' => '',
//                                        'formatting' => 'html',
//                                        'maxlength' => '',
//                                    ),
//
////                                    array (
////                                        'key' => 'field_6189fdebb16a5',
////                                        'label' => __( 'Url', $this -> text_domain ),
////                                        'name' => 'url',
////                                        'type' => 'text',
////                                        'column_width' => '',
////                                        'default_value' => '',
////                                        'placeholder' => '',
////                                        'prepend' => '',
////                                        'append' => '',
////                                        'formatting' => 'html',
////                                        'maxlength' => '',
////                                    ),
//                                ),
//                            ),
//                            array (
//                                'label' => __( 'Select Field', $this -> text_domain ),
//                                'name' => 'ap_select',
//                                'display' => 'table',
//                                'sub_fields' => array (
//                                    array (
//                                        'key' => 'field_6188dc70e0dff',
//                                        'label' => __( 'Name', $this -> text_domain ),
//                                        'name' => 'ap_name',
//                                        'type' => 'text',
////                                        'column_width' => 35,
//                                        'default_value' => '',
//                                        'placeholder' => __( 'Field name', $this -> text_domain ),
//                                        'prepend' => '',
//                                        'append' => '',
//                                        'formatting' => 'html',
//                                        'maxlength' => '',
//                                    ),
//
//                                    array (
//                                        'key' => 'field_52816e92ffee6',
//                                        'label' => __( 'Choices', $this -> text_domain ),
//                                        'name' => 'ap_choices',
//                                        'type' => 'text',
//                                        'instructions' => __( 'Options separated by comma e.g. Petrol, Diesel, Gas, Hybrid', $this -> text_domain ),
//                                        'column_width' => '',
//                                        'default_value' => '',
//                                        'placeholder' => '',
//                                        'prepend' => '',
//                                        'append' => '',
//                                        'formatting' => 'html',
//                                        'maxlength' => '',
//                                    ),
//                                ),
//                            ),
//                            array (
//                                'label' => __( 'Textarea Field', $this -> text_domain ),
//                                'name' => 'ap_textarea',
//                                'display' => 'table',
//                                'sub_fields' => array (
//                                    array (
//                                        'key' => 'field_6188dc70e0dff',
//                                        'label' => __( 'Name', $this -> text_domain ),
//                                        'name' => 'ap_name',
//                                        'type' => 'text',
////                                        'column_width' => 35,
//                                        'default_value' => '',
//                                        'placeholder' => __( 'Field name', $this -> text_domain ),
//                                        'prepend' => '',
//                                        'append' => '',
//                                        'formatting' => 'html',
//                                        'maxlength' => '',
//                                    ),
//                                ),
//                            ),
//                        ),
//                        'button_label' => __( 'Add new custom field', 'acf' ),
//                        'min' => '',
//                        'max' => '',
//                    ),
//                    array (
//                        'key' => 'field_message_custom_fields',
//                        'label' => __( 'Add custom fields', $this -> text_domain ),
//                        'name' => '',
//                        'type' => 'message',
//                        'message' => __( 'The <b>number</b> field requires an upper and lower value boundary for the range to filter. The <b>choices</b> field will ask for a comma-separated list of values.', $this -> text_domain )
//                    ),
//                    array (
//                        'key' => 'field_529db63c010e7',
//                        'label' => __( 'Shortcodes', $this -> text_domain ),
//                        'name' => '',
//                        'type' => 'tab',
//                    ),
//                    array (
//                        'key' => 'field_52a10716e5cdd',
//                        'label' => __( '1. Select products:', $this -> text_domain ),
//                        'name' => 'featured_products',
//                        'type' => 'relationship',
//                        'return_format' => 'object',
//                        'post_type' => array (
//                            0 => 'ap_product',
//                        ),
//                        'taxonomy' => array (
//                            0 => 'all',
//                        ),
//                        'filters' => array (
//                            0 => 'search',
//                        ),
//                        'result_elements' => array (
//                            0 => 'featured_image',
//                            1 => 'post_title',
//                        ),
//                        'max' => '',
//                    ),
//                    array (
//                        'key' => 'field_get_shortcode',
//                        'label' => __( 'Your Shortcode', $this -> text_domain ),
//                        'name' => '',
//                        'type' => 'message',
//                        'message' => __( '2. Copy your generated shortcode:', 'progression-car-dealer ' ),
//                    ),
//                    array (
//                        'key' => 'field_get_shortcode_instructions',
//                        'label' => __( 'Your Shortcode', $this -> text_domain ),
//                        'name' => '',
//                        'type' => 'message',
//                        'message' => __( '3. Paste your shortcode to a text widget or post or page', $this -> text_domain ),
//                    )
                ),

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