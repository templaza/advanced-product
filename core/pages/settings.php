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
                'title' => 'Settings',
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
                    'label' => 'General',
                    'name' => '',
                    'type' => 'tab',
                ),
                array(
                    'key' => 'field_ap_inventory_page_id',
                    'label' => 'Inventory page',
                    'name' => 'ap_inventory_page_id',
                    'type' => 'post_object',
                    'post_type'=> array('page')
                ),
                array (
                    'key' => 'field_5281609138d88',
                    'label' => 'Mileage unit',
                    'name' => 'ap_milage_unit',
                    'type' => 'radio',
                    'choices' => array (
                        'mi' => 'Miles (mi)',
                        'km' => 'Kilometer (km)',
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
                    'label'         => 'Currency',
                    'default_value' => 'USD',
                    'choices'       => array(
                        'ALL' => 'Albanian Lek (L)',
                        'DZD' => 'Algerian Dinar (د.ج)',
                        'AFN' => 'Afghan Afghani (؋)',
                        'ARS' => 'Argentine Peso ($)',
                        'AUD' => 'Australian Dollar ($)',
                        'AZN' => 'Azerbaijani Manat (AZN)',
                        'BSD' => 'Bahamian Dollar ($)',
                        'BHD' => 'Bahraini Dinar (.د.ب)',
                        'BBD' => 'Barbadian Dollar ($)',
                        'BDT' => 'Bangladeshi taka (৳ )',
                        'BYR' => 'Belarusian Ruble (Br)',
                        'BZD' => 'Belize Dollar ($)',
                        'BMD' => 'Bermudian Dollar ($)',
                        'BOB' => 'Bolivian Boliviano (Bs.)',
                        'BAM' => 'Bosnia and Herzegovina Convertible Mark (KM)',
                        'BWP' => 'Botswana Pula (P)',
                        'BGN' => 'Bulgarian Lev (лв.)',
                        'BRL' => 'Brazilian Real (R$)',
                        'GBP' => 'British Pound (£)',
                        'BND' => 'Brunei Dollar ($)',
                        'KHR' => 'Cambodian Riel (៛)',
                        'CAD' => 'Canadian dollar ($)',
                        'KYD' => 'Cayman Islands Dollar ($)',
                        'CLP' => 'Chilean Peso ($)',
                        'CNY' => 'Chinese Yuan (¥)',
                        'COP' => 'Colombian Peso ($)',
                        'CRC' => 'Costa Rican colón (₡)',
                        'HRK' => 'Croatian Kuna (Kn)',
                        'CUP' => 'Cuban Peso ($)',
                        'CZK' => 'Czech Koruna (Kč)',
                        'DOP' => 'Dominican Peso (RD$)',
                        'XCD' => 'East Caribbean Dollar ($)',
                        'EGP' => 'Egyptian Pound (EGP)',
                        'EUR' => 'Euro Member Countries (€)',
                        'FKP' => 'Falkland Islands Pound (£)',
                        'FJD' => 'Fijian Dollar ($)',
                        'GHC' => 'Ghana Cedi (₵)',
                        'GIP' => 'Gibraltar Pound (£)',
                        'GTQ' => 'Guatemalan Quetzal (Q)',
                        'GGP' => 'Guernsey Pound (£)',
                        'GYD' => 'Guyanese Dollar ($)',
                        'GEL' => 'Georgian Lari (ლ)',
                        'HNL' => 'Honduran Lempira (L)',
                        'HKD' => 'Hong Kong Dollar ($)',
                        'HUF' => 'Hungarian Forint (Ft)',
                        'ISK' => 'Icelandic Fróna (kr.)',
                        'INR' => 'Indian Rupee (₹)',
                        'IDR' => 'Indonesian Rupiah (Rp)',
                        'IRR' => 'Iranian Rial (﷼)',
                        'ILS' => 'Israeli New Shekel (₪)',
                        'JMD' => 'Jamaican Dollar ($)',
                        'JPY' => 'Japanese Yen (¥)',
                        'JEP' => 'Jersey Pound (£)',
                        'KZT' => 'Kazakhstani tenge (KZT)',
                        'KPW' => 'North Korean won (₩)',
                        'KRW' => 'South Korean won (₩)',
                        'KGS' => 'Kyrgyzstani som (сом)',
                        'KES' => 'Kenyan shilling (KSh)',
                        'LAK' => 'Lao kip (₭)',
                        'LBP' => 'Lebanese pound (ل.ل)',
                        'LRD' => 'Liberian dollar ($)',
                        'MKD' => 'Macedonian denar (ден)',
                        'MYR' => 'Malaysian ringgit (RM)',
                        'MUR' => 'Mauritian rupee (₨)',
                        'MXN' => 'Mexican peso ($)',
                        'MNT' => 'Mongolian tögrög (₮)',
                        'MAD' => 'Moroccan dirham (د.م.)',
                        'MZN' => 'Mozambican metical (MT)',
                        'NAD' => 'Namibian dollar ($)',
                        'NPR' => 'Nepalese rupee (₨)',
                        'ANG' => 'Netherlands Antillean guilder (ƒ)',
                        'NZD' => 'New Zealand dollar ($)',
                        'NIO' => 'Nicaraguan córdoba (C$)',
                        'NGN' => 'Nigerian naira (₦)',
                        'NOK' => 'Norwegian krone (kr)',
                        'OMR' => 'Omani rial (ر.ع.)',
                        'PKR' => 'Pakistani rupee (₨)',
                        'PAB' => 'Panamanian balboa (B/.)',
                        'PYG' => 'Paraguayan guaraní (₲)',
                        'PEN' => 'Peruvian nuevo sol (S/.)',
                        'PHP' => 'Philippine peso (₱)',
                        'PLN' => 'Polish złoty (zł)',
                        'QAR' => 'Qatari riyal (ر.ق)',
                        'RON' => 'Romanian leu (lei)',
                        'RUB' => 'Russian ruble (₽)',
                        'SHP' => 'Saint Helena pound (£)',
                        'SAR' => 'Saudi riyal (ر.س)',
                        'RSD' => 'Serbian dinar (дин.)',
                        'SCR' => 'Seychellois rupee (₨)',
                        'SGD' => 'Singapore dollar ($)',
                        'SBD' => 'Solomon Islands dollar ($)',
                        'SOS' => 'Somali shilling (Sh)',
                        'ZAR' => 'South African rand (R)',
                        'LKR' => 'Sri Lankan rupee (රු)',
                        'SEK' => 'Swedish krona (kr)',
                        'CHF' => 'Swiss franc (CHF)',
                        'SRD' => 'Surinamese dollar ($)',
                        'SYP' => 'Syrian pound (ل.س)',
                        'TWD' => 'New Taiwan dollar (NT$)',
                        'THB' => 'Thai baht (฿)',
                        'TTD' => 'Trinidad and Tobago dollar ($)',
                        'TRL' => 'Turkish lira (₺)',
                        'UAH' => 'Ukrainian hryvnia (₴)',
                        'AED' => 'United Arab Emirates dirham (د.إ)',
                        'USD' => 'United States dollar ($)',
                        'UYU' => 'Uruguayan peso ($)',
                        'UZS' => 'Uzbekistani som (UZS)',
                        'VEF' => 'Venezuelan bolívar (Bs F)',
                        'VND' => 'Vietnamese đồng (₫)',
                        'YER' => 'Yemeni rial (﷼)',
                    ),
                ),
                array (
                    'key' => 'field_5281610b906e3',
                    'label' => 'Currency symbol',
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
                    'label' => 'Symbol placement',
                    'name' => 'ap_symbol_placement',
                    'type' => 'radio',
                    'choices' => array (
                        'prepend' => 'Before numbers',
                        'append' => 'After numbers',
                    ),
                    'other_choice' => 0,
                    'save_other_choice' => 0,
                    'default_value' => 'prepend',
                    'layout' => 'vertical',
                ),
                array (
                    'key'           => 'field_52816229906e5',
                    'label'         => 'Thousands separator',
                    'name'          => 'ap_price_thousands_sep',
                    'type'          => 'text',
                    'default_value' => '.',
                ),
                array (
                    'key' => 'field_618e30cd79924',
                    'label' => 'Decimal separator',
                    'name' => 'ap_price_decimal_sep',
                    'type' => 'text',
                    'default_value' => ',',
                ),
                array (
                    'key' => 'field_618e37d0481ac',
                    'label' => 'Number of decimals',
                    'name' => 'ap_price_num_decimals',
                    'type' => 'number',
                    'default_value' => '0',
                ),
                array (
                    'key' => 'field_6306f42b88ed8',
                    'label' => 'Archive Product',
                    'name' => '',
                    'type' => 'tab',
                ),
                array (
                    'key'           => 'field_6306f48249024',
                    'type'          => 'radio',
                    'layout'        => 'horizontal',
                    'name'          => 'ap_show_archive_compare_button',
                    'label'         => 'Show Compare Button',
                    'default_value' => 1,
                    'choices'       => array(
                        1   => 'Yes',
                        0   => 'No',
                    ),
                ),
                array (
                    'key'           => 'field_630ccbd5a0a19',
                    'type'          => 'radio',
                    'layout'        => 'horizontal',
                    'name'          => 'ap_show_archive_quickview_button',
                    'label'         => 'Show Quick View Button',
                    'default_value' => 1,
                    'choices'       => array(
                        1   => 'Yes',
                        0   => 'No',
                    ),
                ),
                array (
                    'key'           => 'field_634d1f9ef0431',
                    'type'          => 'radio',
                    'layout'        => 'horizontal',
                    'name'          => 'ap_show_archive_custom_field_icon',
                    'label'         => 'Show Custom Field Icon',
                    'default_value' => 0,
                    'choices'       => array(
                        1   => 'Yes',
                        0   => 'No',
                    ),
                ),
                array (
                    'key'           => 'field_631aac11e8f20',
                    'type'          => 'select',
                    'layout'        => 'horizontal',
                    'name'          => 'ap_archive_product_order_by',
                    'label'         => 'Product Order',
                    'default_value' => 'rdate',
                    'choices'       => array(
                        'rdate'   => 'Most recent first',
                        'date'   => 'Oldest First',
                        'alpha'   => 'Title Alphabetical',
                        'ralpha'   => 'Title Reverse Alphabetical',
                        'author'   => 'Author Alphabetical',
                        'rauthor'   => 'Author Reverse Alphabetical',
                        'hits'   => 'Most Hits',
                        'rhits'   => 'Least Hits',
                        'price'   => 'Minimum Price First',
                        'rprice'   => 'Maximum Price First',
                        'price_rental'   => 'Minimum Rental Price First',
                        'rprice_rental'   => 'Maximum Rental Price First',
                    ),
                ),
                array (
                    'key'           => 'field_631aac11e8f22',
                    'type'          => 'select',
                    'layout'        => 'horizontal',
                    'name'          => 'ap_archive_sold_product_order_by',
                    'label'         => 'Sold Product Order',
                    'default_value' => '',
                    'choices'       => array(
                        ''          => '- Select Sold Product Order -',
                        'top'       => 'Top of the list',
                        'bottom'    => 'Bottom of the list',
                    ),
                ),
                array (
                    'key'           => 'field_633be5d141b34',
                    'type'          => 'select',
                    'layout'        => 'horizontal',
                    'name'          => 'ap_archive_product_order_by_custom_field',
                    'label'         => 'Custom Field Order',
                    'default_value' => 'order',
                    'choices'       => array(
                        'rdate'     => 'Most recent first',
                        'date'      => 'Oldest First',
                        'alpha'     => 'Title Alphabetical',
                        'ralpha'    => 'Title Reverse Alphabetical',
                        'order'     => 'Custom Field Order',
                        'rorder'    => 'Custom Field Reverse Order',
                    ),
                ),
                array (
                    'key' => 'field_61b705ba820c5',
                    'label' => 'Single Product',
                    'name' => '',
                    'type' => 'tab',
                ),
                array(
                    'key'       => 'field_61b70531f3f97',
                    'label'     => 'Show Date',
                    'name'      => 'ap_show_date',
                    'type'      => 'radio',
                    'choices'   => array(
                        1   => 'Yes',
                        0   => 'No',
                    ),
                    'default_value' => 0,
                    'layout'    => 'horizontal',
                ),
                array(
                    'key'       => 'field_61b7054b01a44',
                    'label'     => 'Show Author',
                    'name'      => 'ap_show_author',
                    'type'      => 'radio',
                    'choices'   => array(
                        1   => 'Yes',
                        0   => 'No',
                    ),
                    'default_value' => 0,
                    'layout'    => 'horizontal',
                ),
                array(
                    'key'       => 'field_61b7055a3c27c',
                    'label'     => 'Show Post View',
                    'name'      => 'ap_show_post_view',
                    'type'      => 'radio',
                    'choices'   => array(
                        1   => 'Yes',
                        0   => 'No',
                    ),
                    'default_value' => 0,
                    'layout'    => 'horizontal',
                ),
                array(
                    'key'       => 'field_61b705670c3ab',
                    'label'     => 'Show Comment Count',
                    'name'      => 'ap_show_comment_count',
                    'type'      => 'radio',
                    'choices'   => array(
                        1   => 'Yes',
                        0   => 'No',
                    ),
                    'default_value' => 0,
                    'layout'    => 'horizontal',
                ),
                array (
                    'key'           => 'field_529db63c010e5',
                    'type'          => 'radio',
                    'layout'        => 'horizontal',
                    'name'          => 'ap_show_compare_button',
                    'label'         => 'Show Compare Button',
                    'default_value' => 1,
                    'choices'       => array(
                        1   => 'Yes',
                        0   => 'No',
                    ),
                ),
                array (
                    'key'           => 'field_634e60321041b',
                    'type'          => 'radio',
                    'layout'        => 'horizontal',
                    'name'          => 'ap_show_custom_field_icon',
                    'label'         => 'Show Custom Field Icon',
                    'default_value' => 0,
                    'choices'       => array(
                        1   => 'Yes',
                        0   => 'No',
                    ),
                ),
                array (
                    'key'           => 'field_634e60321452d',
                    'type'          => 'radio',
                    'layout'        => 'horizontal',
                    'name'          => 'ap_show_rating',
                    'label'         => 'Show Rating',
                    'default_value' => 0,
                    'choices'       => array(
                        1   => 'Yes',
                        0   => 'No',
                    ),
                ),
                array (
                    'key'           => 'field_633bfbb3cd73a',
                    'type'          => 'select',
                    'layout'        => 'horizontal',
                    'name'          => 'ap_order_by_custom_field',
                    'label'         => 'Custom Field Order',
                    'default_value' => 'order',
                    'choices'       => array(
                        'rdate'     => 'Most recent first',
                        'date'      => 'Oldest First',
                        'alpha'     => 'Title Alphabetical',
                        'ralpha'    => 'Title Reverse Alphabetical',
                        'order'     => 'Custom Field Order',
                        'rorder'    => 'Custom Field Reverse Order',
                    ),
                ),
            );

            $fields = \apply_filters('advanced-product/settings/fields', $fields);

            call_user_func($register_field_group, array (
                'id' => 'advanced_product_settings_page',
                'title' => 'Settings',
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