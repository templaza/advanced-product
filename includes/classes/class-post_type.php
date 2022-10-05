<?php
namespace Advanced_Product;

defined('ADVANCED_PRODUCT') or exit();

if(!class_exists('Advanced_Product\Post_Type')) {
    class Post_Type extends Base
    {
        protected $prefix       = 'ap_';
        protected $cache        = array();

        public function __construct($core = null, $post_type = null)
        {
            register_activation_hook( ADVANCED_PRODUCT . '/' . ADVANCED_PRODUCT.'.php', array( $this, 'register_post_type' ) );
            register_activation_hook( ADVANCED_PRODUCT . '/' . ADVANCED_PRODUCT.'.php', array( $this, '__register_taxonomy' ) );

            parent::__construct($core, $post_type);
        }

        public function hooks(){
            parent::hooks();

            add_action('init', array($this, 'register_post_type'));
            add_action('init', array($this, '__register_taxonomy'));

            // Manage post type header column list hook
            if(method_exists($this, 'manage_edit_columns')){
                remove_filter('manage_'.$this ->get_post_type().'_posts_columns', array($this, 'manage_edit_columns'));
                add_filter('manage_'.$this ->get_post_type().'_posts_columns', array($this, 'manage_edit_columns'),11);
            }
            // Manage post type content column list hook
            if(method_exists($this, 'manage_custom_column')) {
                remove_action('manage_' . $this->get_post_type() . '_posts_custom_column', array($this, 'manage_custom_column'));
                add_action('manage_' . $this->get_post_type() . '_posts_custom_column', array($this, 'manage_custom_column'), 1, 2);
            }

            if(method_exists($this, 'save_post')) {
                add_action('save_post_' . $this->get_post_type(), array($this, 'save_post'), 10, 3);
            }

            if(method_exists($this, 'admin_enqueue_scripts')) {
                remove_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
                add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
            }
        }

        public function __register_taxonomy(){
            if(method_exists($this, 'register_taxonomy')){
//                $multi_taxs   = $this -> register_taxonomy();
                $tax   = $this -> register_taxonomy();
                if(count($tax)) {
//                    foreach($multi_taxs as $name => $tax) {
                        $tax_name   = isset($tax['taxonomy'])?$tax['taxonomy']:'';
//                        $tax_args   = $tax['args'];
                        $object_type= isset($tax['object_type'])?$tax['object_type']:$this -> get_post_type();

                        \register_taxonomy($tax_name, $object_type, $tax['args']);
//                    }
                }
            }
        }

        // Get post type name by class name
        public function get_post_type(){
            $store_id   = __METHOD__;
            $store_id   = md5($store_id);

            if(isset($this -> cache[$store_id])){
                return $this -> cache[$store_id];
            }

            $class_name = get_class($this);
            $class_name = preg_replace('#^'.addslashes(__CLASS__).'\\\\#i', '', $class_name);
            $class_name = preg_replace('/^Advanced_Product\\\\Post_Type\\\\/i', '', $class_name);
            $class_name = $this -> prefix.strtolower($class_name);


            $this -> cache[$store_id]   = $class_name;

            return $class_name;
        }

        public function my_post_type_exists(){
            $post_type  = $this -> get_post_type();
            if(post_type_exists($post_type) && $this -> get_current_screen_post_type() == $post_type) {
                return true;
            }
            return false;
        }

        public function get_current_screen_post_type() {

            global $post, $typenow, $current_screen;

            if ($post && $post->post_type) return $post->post_type;

            elseif($typenow) return $typenow;

            elseif($current_screen && $current_screen->post_type) return $current_screen->post_type;

            elseif(isset($_REQUEST['post']) && \get_post_type($_REQUEST['post'])) return \get_post_type($_REQUEST['post']);
            elseif(isset($_REQUEST['post_type'])) return sanitize_key($_REQUEST['post_type']);

            return null;

        }

        public function register_post_type(){
            $post_type  = $this->get_post_type();

            if(!post_type_exists($post_type)){
                // Register post type to wordpress
                if(method_exists($this, 'register')) {
                    $post_type_args = $this -> register();
                    \register_post_type($post_type, $post_type_args);

                    if($this -> my_post_type_exists()){
                        do_action('advanced-product/post_type/'.$post_type.'/registered', $post_type, $this);
                    }
                    do_action('advanced-product/post_type/registered', $post_type, $this);
                }
            }
        }
    }
}