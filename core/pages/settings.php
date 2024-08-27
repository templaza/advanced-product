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

        $this -> build_in   = FieldHelper::get_core_fields();

    }

    public function hooks()
    {
        parent::hooks();

        add_action( 'plugins_loaded', array( $this, 'register_admin_fields' ) );
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
                array(
                    'key' => 'field_ap_inventory_page_id',
                    'label' => __('Inventory page', 'advanced-product'),
                    'name' => 'ap_inventory_page_id',
                    'type' => 'post_object',
                    'post_type'=> array('page')
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
                    'key'           => 'field_631aac29e8f20',
                    'type'          => 'select',
                    'layout'        => 'horizontal',
                    'name'          => 'ap_currency',
                    'label'         => __( 'Currency', 'advanced-product' ),
                    'default_value' => 'USD',
                    'choices'       => array(
                        'ALL' => esc_html__('Albanian Lek (L)', 'advanced-product'),
                        'DZD' => esc_html__('Algerian Dinar (د.ج)', 'advanced-product'),
                        'AFN' => esc_html__('Afghan Afghani (؋)', 'advanced-product'),
                        'ARS' => esc_html__('Argentine Peso ($)', 'advanced-product'),
                        'AUD' => esc_html__('Australian Dollar ($)', 'advanced-product'),
                        'AZN' => esc_html__('Azerbaijani Manat (AZN)', 'advanced-product'),
                        'BSD' => esc_html__('Bahamian Dollar ($)', 'advanced-product'),
                        'BHD' => esc_html__('Bahraini Dinar (.د.ب)', 'advanced-product'),
                        'BBD' => esc_html__('Barbadian Dollar ($)', 'advanced-product'),
                        'BDT' => esc_html__('Bangladeshi taka (৳ )', 'advanced-product'),
                        'BYR' => esc_html__('Belarusian Ruble (Br)', 'advanced-product'),
                        'BZD' => esc_html__('Belize Dollar ($)', 'advanced-product'),
                        'BMD' => esc_html__('Bermudian Dollar ($)', 'advanced-product'),
                        'BOB' => esc_html__('Bolivian Boliviano (Bs.)', 'advanced-product'),
                        'BAM' => esc_html__('Bosnia and Herzegovina Convertible Mark (KM)', 'advanced-product'),
                        'BWP' => esc_html__('Botswana Pula (P)', 'advanced-product'),
                        'BGN' => esc_html__('Bulgarian Lev (лв.)', 'advanced-product'),
                        'BRL' => esc_html__('Brazilian Real (R$)', 'advanced-product'),
                        'GBP' => esc_html__('British Pound (£)', 'advanced-product'),
                        'BND' => esc_html__('Brunei Dollar ($)', 'advanced-product'),
                        'KHR' => esc_html__('Cambodian Riel (៛)', 'advanced-product'),
                        'CAD' => esc_html__('Canadian dollar ($)', 'advanced-product'),
                        'KYD' => esc_html__('Cayman Islands Dollar ($)', 'advanced-product'),
                        'CLP' => esc_html__('Chilean Peso ($)', 'advanced-product'),
                        'CNY' => esc_html__('Chinese Yuan (¥)', 'advanced-product'),
                        'COP' => esc_html__('Colombian Peso ($)', 'advanced-product'),
                        'CRC' => esc_html__('Costa Rican colón (₡)', 'advanced-product'),
                        'HRK' => esc_html__('Croatian Kuna (Kn)', 'advanced-product'),
                        'CUP' => esc_html__('Cuban Peso ($)', 'advanced-product'),
                        'CZK' => esc_html__('Czech Koruna (Kč)', 'advanced-product'),
                        'DOP' => esc_html__('Dominican Peso (RD$)', 'advanced-product'),
                        'XCD' => esc_html__('East Caribbean Dollar ($)', 'advanced-product'),
                        'EGP' => esc_html__('Egyptian Pound (EGP)', 'advanced-product'),
                        'EUR' => esc_html__('Euro Member Countries (€)', 'advanced-product'),
                        'FKP' => esc_html__('Falkland Islands Pound (£)', 'advanced-product'),
                        'FJD' => esc_html__('Fijian Dollar ($)', 'advanced-product'),
                        'GHC' => esc_html__('Ghana Cedi (₵)', 'advanced-product'),
                        'GIP' => esc_html__('Gibraltar Pound (£)', 'advanced-product'),
                        'GTQ' => esc_html__('Guatemalan Quetzal (Q)', 'advanced-product'),
                        'GGP' => esc_html__('Guernsey Pound (£)', 'advanced-product'),
                        'GYD' => esc_html__('Guyanese Dollar ($)', 'advanced-product'),
                        'GEL' => esc_html__('Georgian Lari (ლ)', 'advanced-product'),
                        'HNL' => esc_html__('Honduran Lempira (L)', 'advanced-product'),
                        'HKD' => esc_html__('Hong Kong Dollar ($)', 'advanced-product'),
                        'HUF' => esc_html__('Hungarian Forint (Ft)', 'advanced-product'),
                        'ISK' => esc_html__('Icelandic Fróna (kr.)', 'advanced-product'),
                        'INR' => esc_html__('Indian Rupee (₹)', 'advanced-product'),
                        'IDR' => esc_html__('Indonesian Rupiah (Rp)', 'advanced-product'),
                        'IRR' => esc_html__('Iranian Rial (﷼)', 'advanced-product'),
                        'ILS' => esc_html__('Israeli New Shekel (₪)', 'advanced-product'),
                        'JMD' => esc_html__('Jamaican Dollar ($)', 'advanced-product'),
                        'JPY' => esc_html__('Japanese Yen (¥)', 'advanced-product'),
                        'JEP' => esc_html__('Jersey Pound (£)', 'advanced-product'),
                        'KZT' => esc_html__('Kazakhstani tenge (KZT)', 'advanced-product'),
                        'KPW' => esc_html__('North Korean won (₩)', 'advanced-product'),
                        'KRW' => esc_html__('South Korean won (₩)', 'advanced-product'),
                        'KGS' => esc_html__('Kyrgyzstani som (сом)', 'advanced-product'),
                        'KES' => esc_html__('Kenyan shilling (KSh)', 'advanced-product'),
                        'LAK' => esc_html__('Lao kip (₭)', 'advanced-product'),
                        'LBP' => esc_html__('Lebanese pound (ل.ل)', 'advanced-product'),
                        'LRD' => esc_html__('Liberian dollar ($)', 'advanced-product'),
                        'MKD' => esc_html__('Macedonian denar (ден)', 'advanced-product'),
                        'MYR' => esc_html__('Malaysian ringgit (RM)', 'advanced-product'),
                        'MUR' => esc_html__('Mauritian rupee (₨)', 'advanced-product'),
                        'MXN' => esc_html__('Mexican peso ($)', 'advanced-product'),
                        'MNT' => esc_html__('Mongolian tögrög (₮)', 'advanced-product'),
                        'MAD' => esc_html__('Moroccan dirham (د.م.)', 'advanced-product'),
                        'MZN' => esc_html__('Mozambican metical (MT)', 'advanced-product'),
                        'NAD' => esc_html__('Namibian dollar ($)', 'advanced-product'),
                        'NPR' => esc_html__('Nepalese rupee (₨)', 'advanced-product'),
                        'ANG' => esc_html__('Netherlands Antillean guilder (ƒ)', 'advanced-product'),
                        'NZD' => esc_html__('New Zealand dollar ($)', 'advanced-product'),
                        'NIO' => esc_html__('Nicaraguan córdoba (C$)', 'advanced-product'),
                        'NGN' => esc_html__('Nigerian naira (₦)', 'advanced-product'),
                        'NOK' => esc_html__('Norwegian krone (kr)', 'advanced-product'),
                        'OMR' => esc_html__('Omani rial (ر.ع.)', 'advanced-product'),
                        'PKR' => esc_html__('Pakistani rupee (₨)', 'advanced-product'),
                        'PAB' => esc_html__('Panamanian balboa (B/.)', 'advanced-product'),
                        'PYG' => esc_html__('Paraguayan guaraní (₲)', 'advanced-product'),
                        'PEN' => esc_html__('Peruvian nuevo sol (S/.)', 'advanced-product'),
                        'PHP' => esc_html__('Philippine peso (₱)', 'advanced-product'),
                        'PLN' => esc_html__('Polish złoty (zł)', 'advanced-product'),
                        'QAR' => esc_html__('Qatari riyal (ر.ق)', 'advanced-product'),
                        'RON' => esc_html__('Romanian leu (lei)', 'advanced-product'),
                        'RUB' => esc_html__('Russian ruble (₽)', 'advanced-product'),
                        'SHP' => esc_html__('Saint Helena pound (£)', 'advanced-product'),
                        'SAR' => esc_html__('Saudi riyal (ر.س)', 'advanced-product'),
                        'RSD' => esc_html__('Serbian dinar (дин.)', 'advanced-product'),
                        'SCR' => esc_html__('Seychellois rupee (₨)', 'advanced-product'),
                        'SGD' => esc_html__('Singapore dollar ($)', 'advanced-product'),
                        'SBD' => esc_html__('Solomon Islands dollar ($)', 'advanced-product'),
                        'SOS' => esc_html__('Somali shilling (Sh)', 'advanced-product'),
                        'ZAR' => esc_html__('South African rand (R)', 'advanced-product'),
                        'LKR' => esc_html__('Sri Lankan rupee (රු)', 'advanced-product'),
                        'SEK' => esc_html__('Swedish krona (kr)', 'advanced-product'),
                        'CHF' => esc_html__('Swiss franc (CHF)', 'advanced-product'),
                        'SRD' => esc_html__('Surinamese dollar ($)', 'advanced-product'),
                        'SYP' => esc_html__('Syrian pound (ل.س)', 'advanced-product'),
                        'TWD' => esc_html__('New Taiwan dollar (NT$)', 'advanced-product'),
                        'THB' => esc_html__('Thai baht (฿)', 'advanced-product'),
                        'TTD' => esc_html__('Trinidad and Tobago dollar ($)', 'advanced-product'),
                        'TRL' => esc_html__('Turkish lira (₺)', 'advanced-product'),
                        'UAH' => esc_html__('Ukrainian hryvnia (₴)', 'advanced-product'),
                        'AED' => esc_html__('United Arab Emirates dirham (د.إ)', 'advanced-product'),
                        'USD' => esc_html__('United States dollar ($)', 'advanced-product'),
                        'UYU' => esc_html__('Uruguayan peso ($)', 'advanced-product'),
                        'UZS' => esc_html__('Uzbekistani som (UZS)', 'advanced-product'),
                        'VEF' => esc_html__('Venezuelan bolívar (Bs F)', 'advanced-product'),
                        'VND' => esc_html__('Vietnamese đồng (₫)', 'advanced-product'),
                        'YER' => esc_html__('Yemeni rial (﷼)', 'advanced-product'),
                    ),
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
                        'price_rental'   => __('Minimum Rental Price First', 'advanced-product'),
                        'rprice_rental'   => __('Maximum Rental Price First', 'advanced-product'),
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
                    'key'           => 'field_634e60321452d',
                    'type'          => 'radio',
                    'layout'        => 'horizontal',
                    'name'          => 'ap_show_rating',
                    'label'         => __( 'Show Rating', 'advanced-product' ),
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