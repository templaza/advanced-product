<?php

namespace Advanced_Product\Post_Type;

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\Post_Type;
use Advanced_Product\AP_Functions;
use Advanced_Product\Helper\FieldHelper;
use Advanced_Product\Helper\AP_Product_Helper;
use Advanced_Product\Helper\AP_Custom_Field_Helper;

if(!class_exists('Advanced_Product\Post_Type\Product')){
    class Product extends Post_Type {

        protected $fields;
        protected $built_in;

        public function __construct($core = null, $post_type = null)
        {
            parent::__construct($core, $post_type);

            $this -> fields     = array();
            $this -> built_in   = FieldHelper::get_core_fields();
        }

        public function hooks()
        {
            parent::hooks();

//            add_action( 'advanced-product/after_init', array( $this, 'register_fields' ) );
//            add_action( 'init', array( $this, 'register_fields' ) );
            add_action( 'admin_init', array( $this, 'register_fields' ) );

            add_action( 'wp_ajax_load_custom_fields', array( $this, 'load_custom_fields' ) );
            add_action( 'wp_ajax_nopriv_load_custom_fields', array( $this, 'load_custom_fields' ) );

            add_filter('acf/location/match_field_groups', array($this, 'acf_match_field_groups'), 20);
        }

        public function register(){
            /**
             * Post types
             */
            $singular  = __( 'Product', 'advanced-product' );
            $plural    = __( 'Products', 'advanced-product' );

            $args = array(
                'description'         => __( 'This is where you can create and manage products.', 'advanced-product' ),
                'labels' => array(
//                    'name' 					=> $plural,
                    'name' 					=> __( 'Advanced Products', 'advanced-product' ),
                    'singular_name' 		=> $singular,
                    'menu_name'             => __( 'Advanced Products', 'advanced-product' ),
                    'all_items'             => sprintf( __( 'All %s', 'advanced-product' ), $plural ),
                    'add_new' 				=> __( 'Add New', 'advanced-product' ),
                    'add_new_item' 			=> sprintf( __( 'Add %s', 'advanced-product' ), $singular ),
                    'edit' 					=> __( 'Edit', 'advanced-product' ),
                    'edit_item' 			=> sprintf( __( 'Edit %s', 'advanced-product' ), $singular ),
                    'new_item' 				=> sprintf( __( 'New %s', 'advanced-product' ), $singular ),
                    'view' 					=> sprintf( __( 'View %s', 'advanced-product' ), $singular ),
                    'view_item' 			=> sprintf( __( 'View %s', 'advanced-product' ), $singular ),
                    'search_items' 			=> sprintf( __( 'Search %s', 'advanced-product' ), $plural ),
                    'not_found' 			=> sprintf( __( 'No %s found', 'advanced-product' ), $plural ),
                    'not_found_in_trash' 	=> sprintf( __( 'No %s found in trash', 'advanced-product' ), $plural ),
                    'parent' 				=> sprintf( __( 'Parent %s', 'advanced-product' ), $singular )
                ),
                'supports'            => array( 'title', 'editor', 'thumbnail','excerpt', 'custom-fields','comments' ),
                'hierarchical'        => false,
                'public'              => true,
                'show_ui'             => true,
                'show_in_menu'        => true,
                'show_in_nav_menus'   => true,
                'show_in_admin_bar'   => true,
                'menu_position'       => 20,
//                'menu_icon'           => AP_Functions::get_my_url() . '/assets/images/icon.svg',
                'menu_icon'           => 'dashicons-store',
                'can_export'          => true,
                'has_archive'         => true,
                'exclude_from_search' => false,
                'publicly_queryable'  => true,
                'capability_type'     => 'post',
                'rewrite'			  => array( 'slug' => 'ap-product' )
            );
            return $args;
        }

        /**
         * use this function to add additional fields to a car object
         * @param  array $args
         */
        public function register_field( $args ) {

            // ACF requires a unique key per field so lets generate one
            $key = md5( serialize( $args ));

            if ( empty( $args['type'] )) {
                $args['type'] = 'number';
            }
            $type = $args['type'];

            if ( 'taxonomy' == $type ) {
                $field = wp_parse_args( $args, array(
                    'key' => $key,
                    'label' => '',
                    'name' => '',
                    'type' => 'taxonomy',
                    'instructions' => '',
                    'taxonomy' => '',
                    'field_type' => 'select',
                    'allow_null' => 1,
                    'load_save_terms' => 1,
                    'return_format' => 'id',
                    'multiple' => 0,
                    'sort' => 0,
                    'group' => 'overview'
                ) );
            } else if ( 'radio' == $type ) {
                $field = wp_parse_args( $args, array (
                    'key' => $key,
                    'label' => '',
                    'name' => '',
                    'instructions' => '',
                    'choices' => array(),
                    'other_choice' => 1,
                    'save_other_choice' => 1,
                    'default_value' => '',
                    'layout' => 'horizontal',
                    'sort' => 0,
                    'group' => 'specs'
                ) );
            } else if ( 'checkbox' == $type ) {
                $field = wp_parse_args( $args, array (
                    'key' => $key,
                    'label' => '',
                    'name' => '',
                    'instructions' => '',
                    'choices' => array(),
                    'layout' => 'vertical',
                    'sort' => 0,
                    'group' => 'specs'
                ) );
            } else {
                $field = wp_parse_args( $args, array (
                    'key' => $key,
                    'label' => '',
                    'name' => '',
                    'type' => 'text',
                    'instructions' => '',
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'min' => 0,
                    'max' => '',
                    'step' => '',
                    'sort' => 0,
                    'group' => 'specs'
                ) );
            }
            $field = apply_filters( 'advanced-product/'.$this -> get_post_type().'/register_field', $field );
            $this->fields[$field['name']] = $field;
            AP_Product_Helper::setField($field);

            return $field;
        }

        public function register_fields() {
            if(!function_exists("register_field_group"))
            {
                return;
            }

            if($this -> get_current_screen_post_type() == 'ap_product') {
//                $cfield = AP_Custom_Field_Helper::get_custom_field('ap_branch');
//
//                if(!$cfield){
//                    return;
//                }
//                $acf_f = AP_Custom_Field_Helper::get_custom_field_option_by_id($cfield -> ID);
//
//
//                if(!$acf_f){
//                    return;
//                }


                $acf_fields  = FieldHelper::get_acf_fields_without_group_field(array(
                    'orderby'   => array(
                        '__protected' => 'DESC'
                    )
                ));
                register_field_group(
                    array(
                        'id' => 'acf_'.md5($this -> get_post_type().'_properties'),
                        'title' => __( 'Properties', 'advanced-product' ),
                        'fields' => $acf_fields,
                        'location' => array (
                            array (
                                array (
                                    'param' => 'post_type',
                                    'operator' => '==',
                                    'value' => $this -> get_post_type(),
                                    'order_no' => 0,
                                    'group_no' => 0,
                                ),
                            ),
                        ),
                        'options' => array (
                            'position' => 'normal',
                            'style' => 'default',
                            'layout' => 'default',
//                        'hide_on_screen' => array (
//                            /*'the_content',*/ 'custom_fields'
//                        ),
                            'hide_on_screen' => array(),
                        ),
                        'menu_order' => 0,
                    )
                );



//                // Get branch field option
//                register_field_group(
//                    array(
//                        'id' => 'acf_'.md5('product_branch_property'),
//                        'title' => __( 'Branch Property', 'advanced-product' ),
//                        'fields' => array(
//                            $acf_f
////                            array(
////                                'key'   => $branch_key,
////                                'label' => '',
////                                'name'  => 'ap_branch'
////                            )
//                        ),
//                        'location' => array (
//                            array (
//                                array (
//                                    'param' => 'post_type',
//                                    'operator' => '==',
//                                    'value' => $this -> get_post_type(),
//                                    'order_no' => 0,
//                                    'group_no' => 0,
//                                ),
//                            ),
//                        ),
//                        'options' => array (
//                            'position' => 'side',
//                            'style' => 'default',
//                            'layout' => 'default',
////                        'hide_on_screen' => array (
////                            /*'the_content',*/ 'custom_fields'
////                        ),
//                            'hide_on_screen' => array(),
//                        ),
//                        'menu_order' => 0,
//                    )
//                );

            }


//            $acf_fields = AP_Custom_Field_Helper::get_custom_fields();
//
//            $fields = array();
//
//            if($acf_fields){
//                $prev_term_slug = '';
////                $gid            = '6216fc1bd1117';
//                $gid            = md5('property');
//                $goptions       = array (
//                    'id' => 'acf_product_property',
//                    'title' => __( 'Properties', 'advanced-product' ),
//                    'fields' => $fields,
//                    'location' => array (
//                        array (
//                            array (
//                                'param' => 'post_type',
//                                'operator' => '==',
//                                'value' => $this -> get_post_type(),
//                                'order_no' => 0,
//                                'group_no' => 0,
//                            ),
//                        ),
//                    ),
//                    'options' => array (
//                        'position' => 'normal',
//                        'style' => 'default',
//                        'layout' => 'default',
////                        'hide_on_screen' => array (
////                            /*'the_content',*/ 'custom_fields'
////                        ),
//                        'hide_on_screen' => array(),
//                    ),
//                    'menu_order' => 0,
//                );
//
//                foreach ($acf_fields as $i => $acf_field){
//                    if($acf_f = AP_Custom_Field_Helper::get_custom_field_option_by_id($acf_field -> ID)) {
//                        $next_index = $i + 1;
//                        if((isset($acf_fields[$next_index]) && /*isset($acf_field -> term_slug)
//                                &&*/ $acf_fields[$next_index] -> term_slug != $acf_field -> term_slug)
//                            || ($i == count($acf_fields) -1)){
//                            $goptions['id']     = 'acf_product_'.(!empty($acf_field -> term_slug))?$acf_field -> term_slug:$gid;
//                            $goptions['title']  = (!empty($acf_field -> term_name))?$acf_field -> term_name:__( 'Properties', 'advanced-product' );
//                            $goptions['fields'] = $fields;
//
//                            $goptions['menu_order']  = $i;
//
//                            register_field_group($goptions);
//                            $fields = array();
//                        }
//                        $fields[] = $acf_f;
//                    }
//                }
//            }
        }

        /**
         * Returns all values of given meta key
         * @param  string $key    [description]
         * @param  string $type   [description]
         * @param  string $status [description]
         * @return [type]         [description]
         */
        public function get_meta_values( $key = '', $type = 'post', $status = 'publish' ) {

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

            return $r;
        }

        public function acf_match_field_groups($group){

//            $filter = array(
//                'post_id'	=> $_REQUEST['post_id'],
//                'post_type'	=> $this -> get_post_type()
//            );
//            $metabox_ids = array();
//            $metabox_ids = apply_filters( 'acf/location/match_field_groups', $metabox_ids, $filter );
//            var_dump($GLOBALS['acf_register_field_group']);
////////            var_dump($metabox_ids);
//            die(__FILE__);
            $group[]    = 'pricing';
//            $group[]    = 'ap_acf_pricing';
//            $group[]    = 'product_pricing';
//            $group[]    = 'acf_product_pricing';
//            $group[]    = 'ap_acf_specifications';

            $group[]    = 'specifications';
            return $group;
        }

        public function load_custom_fields(){
            check_admin_referer( 'load_custom_fields','nonce' );

            $branch_slug    = isset($_REQUEST['branch_slug'])?$_REQUEST['branch_slug']:'';

            if($this -> get_current_screen_post_type() != $this -> get_post_type() || empty($branch_slug)){
                wp_send_json_error('', 404);
                wp_die();
            }

            // Get branch by branch_slug
            $branches = get_terms ([
                'slug'     => $branch_slug,
                'taxonomy' => 'ap_branch',
                'hide_empty' => false,
            ] );

            if(!$branches || is_wp_error($branches)){
                echo '';
                wp_die();
            }

            $product_id = isset($_REQUEST['post_id'])?$_REQUEST['post_id']:0;
            $product    = get_post($product_id);
            $gfields    = array();

            // Get all group fields assigned to branch
            foreach ($branches as $branch) {
//                $gfields_assigned = \get_field('group_field_assigned', 'ap_branch_' . $branch->term_id);
                $gfields_assigned = \get_field('group_field_assigned', 'term_' . $branch->term_id);

                if(!empty($gfields_assigned)) {

                    $gid = md5('property');
                    $goptions = array(
                        'id' => 'acf_' . md5('product_property'),
                        'title' => __('Properties', 'advanced-product'),
                        'fields' => array(),
                        'location' => array(
                            array(
                                array(
                                    'param' => 'post_type',
                                    'operator' => '==',
                                    'value' => $this->get_post_type(),
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
                                $goptions['title'] = (!empty($group->name)) ? $group->name : __('Properties', 'advanced-product');
                                $goptions['menu_order'] = $i;
                                $goptions['fields'] = $fields;

                                register_field_group($goptions);

                                // Register metabox
                                $show = true;

                                // priority
                                $priority = 'high';
                                if ($goptions['options']['position'] == 'side') {
                                    $priority = 'core';
                                }
                                $priority = apply_filters('acf/input/meta_box_priority', $priority, $goptions);

                                // add meta box
                                add_meta_box(
                                    'acf_' . $goptions['id'],
                                    $goptions['title'],
                                    array($this, 'ajax_meta_box_input'),
                                    'ap_product',
                                    $goptions['options']['position'],
                                    $priority,
                                    array('field_group' => $goptions, 'show' => $show, 'post_id' => $product_id)
                                );
                            }
                        }
                    }
                }

//                // Get all custom fields of group fields
//                $cfields = AP_Custom_Field_Helper::get_fields_by_group_field_slug($gfields_assigned);
//
//                $fields = array();
//
//                if ($cfields) {
//                    $gid = md5('property');
//                    $goptions = array(
//                        'id' => 'acf_' . md5('product_property'),
//                        'title' => __('Properties', 'advanced-product'),
//                        'fields' => $fields,
//                        'location' => array(
//                            array(
//                                array(
//                                    'param' => 'post_type',
//                                    'operator' => '==',
//                                    'value' => $this->get_post_type(),
//                                    'order_no' => 0,
//                                    'group_no' => 0,
//                                ),
//                            ),
//                        ),
//                        'options' => array(
//                            'position' => 'normal',
//                            'style' => 'default',
//                            'layout' => 'default',
//                            //                        'hide_on_screen' => array (
//                            //                            /*'the_content',*/ 'custom_fields'
//                            //                        ),
//                            'hide_on_screen' => array(),
//                        ),
//                        'menu_order' => 0,
//                    );
//
//                    $m_order = 0;
//                    foreach ($cfields as $i => $acf_field) {
//                        if ($acf_f = AP_Custom_Field_Helper::get_custom_field_option_by_id($acf_field->ID)) {
//                            $next_index = $i + 1;
//                            if ((isset($cfields[$next_index]) && $cfields[$next_index]->term_slug != $acf_field->term_slug)
//                                || ($i == count($cfields) - 1)) {
//                                $goptions['id'] = (!empty($acf_field->term_slug) ? $acf_field->term_slug : $gid);
//                                $goptions['title'] = (!empty($acf_field->term_name)) ? $acf_field->term_name : __('Properties', 'advanced-product');
//                                $goptions['menu_order'] = $m_order;
//
//                                $goptions['fields'] = $fields;
//
//                                register_field_group($goptions);
//
//
//                                $_goptions = $goptions;
//                                unset($_goptions['fields']);
//                                $gfields[] = $_goptions;
//
//                                $fields = array();
//                                $m_order++;
//                            }
//                            $fields[] = $acf_f;
//                        }
//                    }
//                }
            }

//            if (!empty($gfields) && count($gfields)) {
//                foreach ($gfields as $gfield) {
//
//                    $show = true;
//
//                    // priority
//                    $priority = 'high';
//                    if ($gfield['options']['position'] == 'side') {
//                        $priority = 'core';
//                    }
//                    $priority = apply_filters('acf/input/meta_box_priority', $priority, $gfield);
//
//                    // add meta box
//                    add_meta_box(
//                        'acf_' . $gfield['id'],
//                        $gfield['title'],
//                        array($this, 'ajax_meta_box_input'),
//                        'ap_product',
//                        $gfield['options']['position'],
//                        $priority,
//                        array('field_group' => $gfield, 'show' => $show, 'post_id' => $product_id)
//                    );
//                }
//            }

            ob_start();
            do_meta_boxes('ap_product', 'normal', $product);
            $metabox_html = ob_get_contents();
            ob_end_clean();

            echo wp_send_json_success($metabox_html);
            wp_die();
        }

        public function ajax_meta_box_input( $post, $args ){
            // extract $args
            extract( $args );

            // classes
            $class = 'acf_postbox ' . $args['field_group']['options']['layout'];
            $toggle_class = 'acf_postbox-toggle';


            if( ! $args['show'] )
            {
                $class .= ' acf-hidden';
                $toggle_class .= ' acf-hidden';
            }


            // HTML
            if( $args['show'] )
            {
                $fields = apply_filters('acf/field_group/get_fields', array(), $args['field_group']['id']);

                do_action('acf/create_fields', $fields, $args['post_id']);
            }
            else
            {
                echo '<div class="acf-replace-with-fields"><div class="acf-loading"></div></div>';
            }


            // nonce
            echo '<div style="display:none">';
            echo '<input type="hidden" name="acf_nonce" value="' . wp_create_nonce( 'input' ) . '" />';
            ?>
            <script type="text/javascript">
                (function($) {

                    $('#<?php echo $id; ?>').addClass('<?php echo $class; ?>').removeClass('hide-if-js');
                    $('#adv-settings label[for="<?php echo $id; ?>-hide"]').addClass('<?php echo $toggle_class; ?>');

                })(jQuery);
            </script>
            <?php
            echo '</div>';
        }

        public function admin_enqueue_scripts(){
            wp_localize_script('advanced-product_admin_scripts', 'ap_product', array(
                'post_id'       => isset($_REQUEST['post'])?$_REQUEST['post']:'',
                'custom_fields' => array(
                    'nonce' => wp_create_nonce('load_custom_fields')
                ),
            ));
        }


    }
}