<?php

namespace Advanced_Product\Field\Layout;

use Advanced_Product\AP_Functions;
use Advanced_Product\Field_Layout;

defined('ADVANCED_PRODUCT') or exit();

if(!class_exists('Advanced_Product\Field\Layout\Date_Picker')){
    class Date_Picker extends Field_Layout {
        public function hooks(){
            parent::hooks();

            add_action( 'admin_enqueue_scripts', array($this, 'admin_enqueue_script') );
        }

        public function admin_enqueue_script($hook_suffix){
            global $post_type;
            if($hook_suffix == 'post.php' && $post_type == 'ap_product') {

                // localize
//                global $wp_locale;
//                acf_localize_data(
//                    array(
//                        'datePickerL10n' => array(
//                            'closeText'       => _x( 'Done', 'Date Picker JS closeText', 'acf' ),
//                            'currentText'     => _x( 'Today', 'Date Picker JS currentText', 'acf' ),
//                            'nextText'        => _x( 'Next', 'Date Picker JS nextText', 'acf' ),
//                            'prevText'        => _x( 'Prev', 'Date Picker JS prevText', 'acf' ),
//                            'weekHeader'      => _x( 'Wk', 'Date Picker JS weekHeader', 'acf' ),
//                            'monthNames'      => array_values( $wp_locale->month ),
//                            'monthNamesShort' => array_values( $wp_locale->month_abbrev ),
//                            'dayNames'        => array_values( $wp_locale->weekday ),
//                            'dayNamesMin'     => array_values( $wp_locale->weekday_initial ),
//                            'dayNamesShort'   => array_values( $wp_locale->weekday_abbrev ),
//                        ),
//                    )
//                );

                // script
                wp_enqueue_script( 'jquery-ui-datepicker' );

                // style
                wp_enqueue_style( 'acf-datepicker',AP_Functions::get_my_url()
                    . '/core/includes/library/acf_custom/fields/date_picker/jquery-ui.min.css',
                    array(), '1.11.4' );
                wp_add_inline_script('advanced-product_admin_scripts', '
                    jQuery( function($) {
                        $( ".acf-date_picker" ).datepicker();
                      } );
                  ');
            }
        }
    }
}

new Date_Picker();

?>