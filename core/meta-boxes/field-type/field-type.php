<?php

namespace Advanced_Product\Meta_Box;

use Advanced_Product\Meta_box;
use Advanced_Product\Application;
use Advanced_Product\Helper\AP_Custom_Field_Helper;

defined('ADVANCED_PRODUCT') or exit();

class Field_Type extends Meta_box {

    protected $directory= 'field-type';

    public function hooks()
    {
        parent::hooks(); // TODO: Change the autogenerated stub

        // actions
        add_action('admin_enqueue_scripts', array($this,'admin_enqueue_scripts'));
        add_action('acf/create_field_options', array($this,'acf_create_field_options'), 10);

        add_action('acf/create_field_options', array($this,'create_field_search_options'), 11);
        add_action('advanced-product/field/create_field_options', array($this,'create_field_search_options'), 12);

        add_action('wp_ajax_advanced-product/field_layouts/render_search_options', array($this,'ajax_render_search_options'), 10);

        add_filter('manage_ap_custom_field_posts_columns', array($this, 'manage_edit_columns'));
        add_action('manage_ap_custom_field_posts_custom_column', array($this, 'manage_custom_column'), 10, 2);

    }

    public function register(){
        return array(
            'title'         => __('Field Type', 'advanced-product'),
            'name'          => 'fields',/* */
            'context'       => 'normal', // normal, advanced, side
            'priority'      => 'high', // high, core, default, low - Priorities of placement
            'screen'        => 'ap_custom_field', /*The screen or screens on which to show the box (such as a post type, 'link', or 'comment')*/
        );
    }

    public function acf_create_field_options($field){
        $key    = $field['name'];

        // get name of all fields for use in field type drop down
        $field_types = apply_filters('acf/registered_fields', array());

        $f_types_choices    = array();

        switch ($field['type']){
            case 'radio':
            case 'select':
            case 'checkbox':
            case 'taxonomy':
                if(isset($field_types[__("Choice",'acf')])) {
                    $f_types_choices = $field_types[__("Choice",'acf')];
                }
                unset($f_types_choices['true_false']);
                break;
            case 'textarea':
                $f_types_choices['text']    = $field_types[__("Basic",'acf')]['text'];
                break;
            case 'number':
                $f_types_choices['number']      = $field_types[__("Basic",'acf')]['number'];
                $f_types_choices['select']      = $field_types[__("Choice",'acf')]['select'];
                $f_types_choices['range_slider'] = __('Range Slider', 'advanced-product');
                break;
        }

        $search_type_val    = isset($field['s_type']) && !empty($field['s_type']) ? $field['s_type'] : $field['type'];
        ?>
        <?php
        if(!empty($f_types_choices)) {
            ?>
            <tr class="field_search_type field_option field_option_<?php echo $field['type']; ?>">
                <td class="label">
                    <label><?php _e("Search Field Type", 'advanced-product'); ?></label>
                </td>
                <td>

                    <?php
                    do_action('acf/create_field', array(
                        'type' => 'select',
                        'name' => 'fields[' . $key . '][s_type]',
                        'value' => $search_type_val,
                        'choices' => $f_types_choices
                    ));
                    ?>
                </td>
            </tr>

            <?php
        }
    }

    public function create_field_search_options($field){
        //
        $s_type    = isset($field['s_type']) && !empty($field['s_type']) ? $field['s_type'] : $field['type'];
        do_action('advanced-product/field/create_field_search_options/type='.$s_type, $field);
    }

    public function manage_edit_columns($columns){
        $new_columns            = array();
        $new_columns['cb']      = $columns['cb'];
        $new_columns['title']   = $columns['title'];
        if(isset($columns['protected'])) {
            $new_columns['protected'] = $columns['protected'];
        }

        $new_columns['field_name']   = __('Field Name', 'advanced-product');
        $new_columns['field_type']   = __('Field type', 'advanced-product');
        $new_columns['taxonomy-ap_group_field']      = $columns['taxonomy-ap_group_field'];
        $new_columns['field_instructions']   = __('Instruction', 'advanced-product');

        return array_merge($new_columns, $columns);
    }

    public function manage_custom_column($column, $post_id ){
        if($column == 'field_name' || $column == 'field_type' || $column == 'field_instructions'){
            // get fields
            $fields = apply_filters('acf/field_group/get_fields', array(), $post_id);

            $field  = false;
            if($fields){
                $field  = $fields[0];
            }

            $f_name = preg_replace('/^field_/', '', $column);
            if($field && isset($field[$f_name])){
                echo $field[$f_name];
            }
        }
    }

    public function save_meta_box( $post_id, $post )
    {
        // Check if user has permissions to save data.
        if (!$this->can_save($post_id, $post)) {
            return;
        }

        $reg_args   = $this -> register();
        if(!isset($reg_args['screen']) || (isset($reg_args['screen']) && $post -> post_type != $reg_args['screen'])){
            return;
        }

        /*
            *  save fields
            */
        // vars
        $dont_delete = array();

        if( isset($_POST['fields']) && is_array($_POST['fields']) )
        {
            $i = -1;

            // remove clone field
            unset( $_POST['fields']['field_clone'] );

            $field_data = current($_POST['fields']);

            $fname  = sanitize_text_field($field_data['name']);

            // Validate field name
            $acf_field  = AP_Custom_Field_Helper::get_custom_field($fname, array('exclude_post_id' => $post_id));
            if(!empty($acf_field)) {
                $app = Application::get_instance();
                $app->enqueue_message(sprintf(__('The field name %s is already being used by the %s custom field.',
                    'advanced-product'), $fname, $acf_field -> post_title), 'error');
                return false;
            }


            // loop through and save fields
            foreach( $_POST['fields'] as $key => $field )
            {
                $i++;

                $field['name']  = sanitize_text_field($field['name']);

                // order + key
                $field['order_no'] = $i;
                $field['key'] = $key;

                // Set protected field
                $protected_fields   = AP_Custom_Field_Helper::get_protected_fields_registered();
                $protected          = AP_Custom_Field_Helper::is_protected_field($post_id);

                if(empty($protected) && $protected_fields && in_array($field['name'], $protected_fields)){
                    update_post_meta($post_id, '__protected', 1);
                }

                // save
                do_action('acf/update_field', $field, $post_id );


                // add to dont delete array
                $dont_delete[] = $field['key'];
            }
        }
        unset( $_POST['fields'] );

        // delete all other field
//        $keys = get_post_custom_keys($post_id);
//        if(!empty($keys) && count($keys)){
//            foreach( $keys as $key )
//            {
//                if( strpos($key, 'field_') !== false && !in_array($key, $dont_delete) )
//                {
//                    // this is a field, and it wasn't found in the dont_delete array
//                    do_action('acf/delete_field', $post_id, $key);
//                }
//            }
//        }
    }

    public function admin_enqueue_scripts(){
//        $acf_field_group    = new \acf_field_group();
////        $acf_field_group -> admin_enqueue_scripts();
//
//
//        // custom scripts
//        wp_enqueue_script(array(
//            'acf-field-group',
//        ));
//
//
//        // custom styles
//        wp_enqueue_style(array(
//            'acf-global',
//            'acf-field-group',
//        ));


        // actions
//        do_action('acf/field_group/admin_enqueue_scripts');
//        add_action('admin_head', array($acf_field_group,'admin_head'));

//        global $wp_scripts;

//        var_dump($wp_scripts);
//        die(__METHOD__);

//        // custom scripts
//        wp_enqueue_script(array(
//            'acf-field-group',
//        ));
//
//        // custom styles
//        wp_enqueue_style(array(
//            'acf-global',
//            'acf-field-group',
//        ));


        // actions
//        do_action('acf/field_group/admin_enqueue_scripts');
//        add_action('admin_head', array($this,'admin_head'));



    }

    public function ajax_render_search_options()
    {
        // vars
        $options = array(
            'field_key' => '',
            'field_type' => '',
            'field_search_type' => '',
            'post_id' => 0,
            'nonce' => ''
        );

        // load post options
        $options = array_merge($options, $_POST);

        // verify nonce
        if( ! wp_verify_nonce($options['nonce'], 'acf_nonce') )
        {
            die(0);
        }


        // required
        if( ! $options['field_type'] )
        {
            die(0);
        }

        // find key (not actual field key, more the html attr name)
        $options['field_key'] = str_replace("fields[", "", $options['field_key']);
        $options['field_key'] = str_replace("][s_type]", "", $options['field_key']) ;


        // render options
        $field = array(
            'type' => $options['field_type'],
            'name' => $options['field_key'],
            's_type' => $options['field_search_type']
        );

//        do_action('acf/create_field_options', $field );
        do_action('advanced-product/field/create_field_options', $field );

        die();

    }

}