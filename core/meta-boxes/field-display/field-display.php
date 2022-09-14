<?php

namespace Advanced_Product\Meta_Box;

use Advanced_Product\Meta_box;

defined('ADVANCED_PRODUCT') or exit();

class Field_Display extends Meta_box {

    protected $directory= 'field-display';

    public function register(){
        return array(
            'title'         => __('Field Display', 'advanced-product'),
            'name'          => 'fields',/* */
            'context'       => 'side', // normal, advanced, side
            'priority'      => 'default', // high, core, default, low - Priorities of placement
            'screen'        => 'ap_custom_field', /*The screen or screens on which to show the box (such as a post type, 'link', or 'comment')*/
        );
    }

    public function register_fields(){
//        if(function_exists("register_field_group"))
//            {
//                register_field_group(array (
//                    'id' => 'acf_'.$this -> get_meta_box_name().'_property',
//                    'title' => __( 'Field Display', 'advanced-product' ),
//                    'fields' => array(
////                        array(
////                            'key'   => 'field_61a5e21179a88',
////                            'name'  => 'show_in_excerpt',
////                            'type'  => 'radio',
////                            'label' => __('Show in Excerpt view', 'advanced-product'),
////                            'layout'	=>	'horizontal',
////                            'choices'	=>	array(
////                                1	=>	__("Yes", 'advanced-product'),
////                                0	=>	__("No", 'advanced-product'),
////                            ),
////                        ),
//                        array(
//                            'key'   => 'field_61a5e4c9c3c36',
//                            'name'  => 'show_in_listing',
//                            'type'  => 'radio',
//                            'label' => __('Show in listing view', 'advanced-product'),
//                            'layout'	=>	'horizontal',
//                            'choices'	=>	array(
//                                1	=>	__("Yes", 'advanced-product'),
//                                0	=>	__("No", 'advanced-product'),
//                            ),
//                        ),
//                        array(
//                            'key'   => 'field_61a5e4d69b4c4',
//                            'name'  => 'show_in_search',
//                            'type'  => 'radio',
//                            'label' => __('Show in search view', 'advanced-product'),
//                            'layout'	=>	'horizontal',
//                            'choices'	=>	array(
//                                1	=>	__("Yes", 'advanced-product'),
//                                0	=>	__("No", 'advanced-product'),
//                            ),
//                        ),
//                    ),
//                    'location' => array (
////                        array (
////                            array (
////                                'param' => 'post_type',
////                                'operator' => '==',
////                                'value' => $this -> get_post_type(),
////                                'order_no' => 0,
////                                'group_no' => 0,
////                            ),
////                        ),
//                    ),
//                    'options' => array (
//                        'position' => 'side',
//                        'style' => 'default',
////                        'layout' => 'no_box',
////                        'hide_on_screen' => array (
////                            /*'the_content',*/ 'custom_fields'
////                        ),
//                        'hide_on_screen' => array(),
//                    ),
//                    'menu_order' => 0,
//                ));
//            }

        return array(
            array(
                'key'   => 'field_61a5e4c9c3c36',
                'name'  => 'show_in_listing',
                'type'  => 'radio',
                'label' => __('Show in listing view', 'advanced-product'),
                'layout'	=>	'horizontal',
                'choices'	=>	array(
                    1	=>	__("Yes", 'advanced-product'),
                    0	=>	__("No", 'advanced-product'),
                ),
            ),
            array(
                'key'   => 'field_61a5e4d69b4c4',
                'name'  => 'show_in_search',
                'type'  => 'radio',
                'label' => __('Show in search view', 'advanced-product'),
                'layout'	=>	'horizontal',
                'choices'	=>	array(
                    1	=>	__("Yes", 'advanced-product'),
                    0	=>	__("No", 'advanced-product'),
                ),
            ),
        );
    }

    public function save_meta_box( $post_id, $post )
    {
        // Check if user has permissions to save data.
        if (!$this->can_save($post_id, $post)) {
            return;
        }

        /*
        *  save fields
        */
        $f_name = $this -> get_meta_box_name();

        if( isset($_POST[$f_name]) && is_array($_POST[$f_name]) )
        {
            // loop through and save fields
            foreach( $_POST[$f_name] as $k => $v )
            {
                update_post_meta($post_id, $k, $v);
            }
        }
    }

}