<?php
namespace Advanced_Product;

//use Advanced_Product\Helper\FieldHelper;
//use Advanced_Product\Helper\TaxonomyHelper;

defined('ADVANCED_PRODUCT') or exit();

if(!class_exists('Advanced_Product\ACF_Taxonomy')) {
    class ACF_Taxonomy extends Base
    {
        protected $prefix           = 'ap_';
        protected $acf_input_controller = array();

        protected $taxonomy_name;
        protected $field_registered;

        public function __construct($core = null, $post_type = null, $args = array())
        {
            global $pagenow;

            if(isset($args['field_registered'])){
                $this -> field_registered = $args['field_registered'];
            }

            if(isset($args['taxonomy_name'])){
                $this -> taxonomy_name = $args['taxonomy_name'];
            }

            // Register acf input controller to add fields with edit term taxonomy
            if($pagenow == 'term.php') {
                $this->acf_input_controller = new \acf_controller_input();
            }

            parent::__construct($core, $post_type);
        }



        public function hooks(){
            parent::hooks();

            add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));

            // Render custom fields with edit term taxonomy (acf v4 not supported)
            add_action( "{$this -> taxonomy_name}_edit_form", array( $this, 'render_fields' ), 10, 1 );

            add_action( 'edit_term', array( $this, 'save_term' ), 11, 3 );

            add_filter('acf/load_value', array($this, 'load_value'), 11, 3);
        }

        public function load_value($value, $_post_id, $field){
            global $pagenow;

            if($pagenow == 'term.php' && isset($_GET['taxonomy'])) {
//                $queried_object = get_queried_object();
//                $term_id = $queried_object->term_id;
                $tax     = $_GET['taxonomy'];

                $term_id = $_GET['tag_ID'];

                // Get field value
                $value = get_option($tax.'_'.$term_id.'_'.$field['name']);

                if($field['name'] == 'image'){
                    $value  = (int) $value;
                }
            }

            return $value;
        }

        protected function _get_field_group_id_registered(){
            $field_groups   = $this -> field_registered;

            $store_id   = $this -> _get_store_id(__METHOD__, $field_groups);
            if(isset($this -> cache[$store_id])){
                return $this -> cache[$store_id];
            }

            if(!count($field_groups) || !isset($field_groups['id'])){
                return array();
            }

            $this -> cache[$store_id]   = $field_groups['id'];
            return $field_groups['id'];
        }

        /* Render acf fields for edit term taxonomy */
        public function render_fields( $taxonomy){

            $filter = array(
                'ef_taxonomy' => $taxonomy -> taxonomy
            );
            $group_ids = array();
            $group_ids = apply_filters( 'acf/location/match_field_groups', $group_ids, $filter );

            $acfs = apply_filters('acf/get_field_groups', array());

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

                    $fields = apply_filters('acf/field_group/get_fields', array(), $acf['id']);

                    if($fields && count($fields)) {
                        do_action('acf/create_fields', $fields, $acf['id']);

                        $input_controller = $this->acf_input_controller;
                        $input_controller->input_admin_enqueue_scripts();
                        $input_controller->input_admin_head();
                    }
                }
            }
        }

        public function save_term( $term_id, $tt_id, $taxonomy ) {

            $fields = isset($_POST['fields'])?$_POST['fields']:array();

            // loop through and save
            if( $fields )
            {
                // loop through and save $_POST data
                foreach( $_POST['fields'] as $k => $v )
                {
                    // get field
                    $f = apply_filters('acf/load_field', false, $k );


                    $acf    = new \acf_field_functions();

                    // update field
                    do_action('acf/update_value', $v, $taxonomy.'_'.$term_id, $f );

                }
            }

        }

        public function admin_enqueue_scripts(){
            if($this -> acf_input_controller) {
                $this->acf_input_controller->input_admin_enqueue_scripts();
            }
        }
    }
}